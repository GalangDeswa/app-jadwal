<?php

namespace App\Services;

use App\Models\Room;

class RoomsService extends AbstractService
{
    /*
     * The model to be used by this service.
     *
     * @var \App\Models\Room
     */
    protected $model = Room::class;

    /**
     * Show resources with their relations.
     *
     * @var bool
     */
    protected $showWithRelations = true;


     public function storev2($data = [])
    {
        $room = Room::create([
            'name' => $data['name'],
            'capacity' => $data['capacity'],
            'room_type' => $data['room_type'],
        ]);

        if (!$room) {
            return null;
        }

        return $room;
    }


     public function updatev2($id, $data = [])
    {
        $room = Room::find($id);

        if (!$room) {
            return null;
        }

        $room->update([
             'name' => $data['name'],
             'capacity' => $data['capacity'],
             'room_type' => $data['room_type'],
        ]);



        return $room;
    }
}