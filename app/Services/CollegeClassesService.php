<?php

namespace App\Services;

use DB;
use App\Models\CollegeClass;
use App\Models\Room;

class CollegeClassesService extends AbstractService
{
    /*
     * The model to be used by this service.
     *
     * @var \App\Models\CollegeClass
     */
    protected $model = CollegeClass::class;

    /**
     * Show resources with their relations.
     *
     * @var bool
     */
    protected $showWithRelations = true;

    protected $customFilters = [
        'no_course' => 'getClassesWithNoCourse'
    ];

    /**
     * Get a listing of college classes with necessary filtering
     * applied
     *
     */
    public function all($data = [])
    {
        $classes = parent::all($data);

        return $classes;
    }

    /**
     * Add a new college class
     *
     * @param array $data Data for creating a new college class
     * @return App\Models\CollegeClass Newly created class
     */
      public function store($data = [])
{
    // Retrieve the available rooms and their capacities, excluding specific room types
    $excludedRoomTypes = ['magang', 'KKN'];
    $unavailableRoomIds = $data['unavailable_rooms'] ?? [];
    
    $rooms = Room::whereNotIn('id', $unavailableRoomIds)
                 ->whereNotIn('room_type', $excludedRoomTypes)
                 ->get();

    // Get the maximum room capacity
    $maxRoomCapacity = $rooms->max('capacity');
    
    // Calculate the new size
    $newSize = $data['size'];

    // Check if the new class size exceeds the maximum room capacity
    if ($newSize > $maxRoomCapacity) {
        // Calculate the number of classes needed
        $numberOfClasses = ceil($newSize / $maxRoomCapacity);
        $classes = [];

        // Calculate balanced split sizes
        $baseSize = floor($newSize / $numberOfClasses);
        $extraStudents = $newSize % $numberOfClasses;

        for ($i = 0; $i < $numberOfClasses; $i++) {
            // Determine the size for this class
            $classSize = $baseSize + ($i < $extraStudents ? 1 : 0); // Distribute extra students

            $newClass = CollegeClass::create([
                'name' => $data['name'] . ' ' . chr(65 + $i), // Appending A, B, C, etc.
                'size' => $classSize
            ]);

            if (!$newClass) {
                return null; // Class creation failed
            }

            // Sync unavailable rooms and courses
            if (!empty($unavailableRoomIds)) {
                $newClass->unavailable_rooms()->sync($unavailableRoomIds);
            }

            $newClass->courses()->sync($data['courses']);
            $classes[] = $newClass;
        }

        return $classes; // Return all newly created classes
    } else {
        // If the size does not exceed the room capacity, create a single class
        $class = CollegeClass::create([
            'name' => $data['name'],
            'size' => $newSize
        ]);

        if (!$class) {
            return null; // Class creation failed
        }

        $class->unavailable_rooms()->sync($unavailableRoomIds);
        $class->courses()->sync($data['courses']);

        return [$class]; // Return as an array
    }
}

    /**
     * Get class with given id
     *
     * @param int $id The class' id
     */
    public function show($id)
    {
        $class = parent::show($id);

        if (!$class) {
            return null;
        }

        $roomIds = [];

        foreach ($class->unavailable_rooms as $room) {
            $roomIds[] = $room->id;
        }

        $class->room_ids = $roomIds;

        return $class;
    }

    /**
     * Update the class with the given id
     *
     * @param int $id The ID of the class
     * @param array $data Data
     */
    // public function update($id, $data = [])
    // {
    //     $class = CollegeClass::find($id);

    //     if (!$class) {
    //         return null;
    //     }

    //     $class->update([
    //         'name' => $data['name'],
    //         'size' => $data['size']
    //     ]);

    //     if (!isset($data['unavailable_rooms'])) {
    //         $data['unavailable_rooms'] = [];
    //     }

    //     if (!isset($data['courses'])) {
    //         $data['courses'] = [];
    //     }

    //     $class->unavailable_rooms()->sync($data['unavailable_rooms']);
    //     $class->courses()->sync($data['courses']);

    //     return $class;
    // }


    /**
 * Update the class with the given id
 *
 * @param int $id The ID of the class
 * @param array $data Data
 * @return array|null Array of updated classes or null if not found
 */
public function update($id, $data = [])
{
    // Find the existing class
    $class = CollegeClass::find($id);

    if (!$class) {
        return null; // Class not found
    }

    // Define the room types to exclude
    $excludedRoomTypes = ['magang', 'KKN'];

    // Retrieve the available rooms, excluding specific room types
    $unavailableRoomIds = $data['unavailable_rooms'] ?? [];
    $rooms = Room::whereNotIn('id', $unavailableRoomIds)
                 ->whereNotIn('room_type', $excludedRoomTypes)
                 ->get();

    // Get the maximum room capacity
    $maxRoomCapacity = $rooms->max('capacity');

    // Calculate the new size
    $newSize = $data['size'];

    // Check if the new class size exceeds the maximum room capacity
    if ($newSize > $maxRoomCapacity) {
        // Calculate the number of classes needed
        $numberOfClasses = ceil($newSize / $maxRoomCapacity);
        $classes = [];

        // Remove the existing class and create new split classes
        $class->unavailable_rooms()->detach();
        $class->courses()->detach();
        $class->delete(); // Delete the original class

        // Calculate balanced split sizes
        $baseSize = floor($newSize / $numberOfClasses);
        $extraStudents = $newSize % $numberOfClasses;

        for ($i = 0; $i < $numberOfClasses; $i++) {
            // Determine the size for this class
            $classSize = $baseSize + ($i < $extraStudents ? 1 : 0); // Distribute extra students

            // Create a new class with additional attributes
            $newClass = CollegeClass::create(array_merge($data, [
                'name' => $data['name'] . ' ' . chr(65 + $i), // Appending A, B, C, etc.
                'size' => $classSize,
            ]));

            if (!$newClass) {
                return null; // Class creation failed
            }

            // Sync unavailable rooms and courses
            if (!empty($unavailableRoomIds)) {
                $newClass->unavailable_rooms()->sync($unavailableRoomIds);
            }

            $newClass->courses()->sync($data['courses']);
            $classes[] = $newClass;
        }

        return $classes; // Return all newly created classes
    } else {
        // If the size does not exceed the room capacity, simply update the existing class
        $class->fill($data); // Fill other attributes from data
        $class->size = $newSize;
        $class->save();

        // Sync unavailable rooms and courses
        $class->unavailable_rooms()->sync($unavailableRoomIds);
        $class->courses()->sync($data['courses']);

        return [$class]; // Return as an array
    }
}

    /**
     * Return query with filter applied to select classes with no course added for them
     */
    public function getClassesWithNoCourse($query)
    {
        return $query->havingNoCourses();
    }
}