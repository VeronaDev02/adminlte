<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SelfsSeeder extends Seeder
{
    public function run()
    {
        DB::table('selfs')->insert([
            'sel_id' => 60,
            'sel_name' => 'PDV 05',
            'sel_pdv_ip' => '192.168.104.205',
            'sel_rtsp_url' => 'rtsp://progrmadorl04:v3r0nal004@192.168.101.250:554/cam/realmonitor?channel=2&subtype=0',
            'sel_status' => '1',
            'sel_uni_id' => 1,
            'created_at' => '2025-04-09 17:19:02',
            'updated_at' => '2025-04-09 18:36:06',
        ]);
        DB::table('selfs')->insert([
            'sel_id' => 62,
            'sel_name' => 'PDV 18',
            'sel_pdv_ip' => '192.168.104.218',
            'sel_rtsp_url' => 'rtsp://progrmadorl04:v3r0nal004@192.168.101.250:554/cam/realmonitor?channel=4&subtype=0',
            'sel_status' => '1',
            'sel_uni_id' => 1,
            'created_at' => '2025-04-09 17:19:42',
            'updated_at' => '2025-04-09 17:19:42',
        ]);
        DB::table('selfs')->insert([
            'sel_id' => 61,
            'sel_name' => 'PDV 16',
            'sel_pdv_ip' => '192.168.104.216',
            'sel_rtsp_url' => 'rtsp://progrmadorl04:v3r0nal004@192.168.101.250:554/cam/realmonitor?channel=3&subtype=0',
            'sel_status' => '1',
            'sel_uni_id' => 1,
            'created_at' => '2025-04-09 17:19:25',
            'updated_at' => '2025-04-09 17:19:44',
        ]);
        DB::table('selfs')->insert([
            'sel_id' => 59,
            'sel_name' => 'PDV 01',
            'sel_pdv_ip' => '192.168.104.201',
            'sel_rtsp_url' => 'rtsp://progrmadorl04:v3r0nal004@192.168.101.250:554/cam/realmonitor?channel=1&subtype=0',
            'sel_status' => '1',
            'sel_uni_id' => 1,
            'created_at' => '2025-04-09 17:18:26',
            'updated_at' => '2025-04-09 18:08:36',
        ]);
    }
}
