<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SelfsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('selfs')->delete();
        
        \DB::table('selfs')->insert(array (
            0 => 
            array (
                'sel_id' => 46,
                'sel_name' => 'PDV 18',
                'sel_pdv_ip' => 'eyJpdiI6IjV0Y1RUbFpWRWtrMkpqbUlzcVFQUEE9PSIsInZhbHVlIjoialU0cVBTVkl1cjZaUWU1YjdoaXhrUT09IiwibWFjIjoiMTdiNmY1NWE0ZDRhMmEwZDgxODc2N2U5ZWVkOTVlNjZhNDdjZTk5ZDRlNTJmNjI4ZTY5N2NlYTg0NDM5ZDg4MCIsInRhZyI6IiJ9',
                'sel_status' => true,
                'sel_uni_id' => 31,
                'created_at' => '2025-04-28 08:08:01',
                'updated_at' => '2025-05-02 14:08:12',
                'sel_dvr_ip' => 'eyJpdiI6IlVwU0kvc21oWUQxQVRQWHpZS1ZDZnc9PSIsInZhbHVlIjoiemg3c3JVeHdHMXRvcnUyb1ZUaXRxQT09IiwibWFjIjoiMjNjMzVhNjc2ZGJlZGYxZjdmZDgyYjk1NWFmMjMzYTMyZGFlYjg4Y2Q2MzRhZGJlZjE5MDZiYjY0MjhlZWFlOCIsInRhZyI6IiJ9',
                'sel_dvr_username' => 'eyJpdiI6InVPMTZEL1hGZjFremhwNURzb0xFbmc9PSIsInZhbHVlIjoiVm5LNCt0ZmozSkZuekJweCtScCtYdz09IiwibWFjIjoiNDY1M2NmY2MwMDRmODY4NTA4MGFiYzFiZmIyZDNlZGQ1MWJjM2Q3YTQyMWRiNWRkZjc4YWFlODMwZWYxODU2MSIsInRhZyI6IiJ9',
                'sel_dvr_password' => 'eyJpdiI6Ik5OYVd4UEE0ZTVNVktuQnpITytxMnc9PSIsInZhbHVlIjoiUG9IY1YwczNad2U5SjVVWVUwUFZydz09IiwibWFjIjoiZjMzNDgyOGMyMmQ3ODRiNzNkYTc3YjkxZGRhNjk5MmNiYTY3NmJmMTNjZjczZDJhMzFlMTNmOTEyNDUyZTdjNCIsInRhZyI6IiJ9',
                'sel_camera_canal' => '4',
                'sel_dvr_porta' => 554,
                'sel_rtsp_path' => 'eyJpdiI6IjliNDJoNjNiZitZWlFVNWk5WENrdHc9PSIsInZhbHVlIjoiYkdMSmpqSnVzSVdxZXNlQVd0eVIwV0xrbURLYTJJcFloTWNMTVFXSFdlTkh4dURLcU9IZlIxTG9PT3RqWmdDQXdZaGxmVGhhK1BibFNjdU5MbFJKb0FCUVZKWEFOS2dtVmY0SHc3NlpNM1ZLYmV1cG9BME1CaDl1MnUvY2JDQ3giLCJtYWMiOiI5MWJjZTk5MjZkZDQ1NDBjYTIxNzVmM2NkMGQ0OGIwNjdjMDU1ZDMzY2EwOTljYjZmMGNmMmRmNGIxYzhkY2RlIiwidGFnIjoiIn0=',
                'sel_pdv_codigo' => '218',
            ),
            1 => 
            array (
                'sel_id' => 45,
                'sel_name' => 'PDV 16',
                'sel_pdv_ip' => 'eyJpdiI6IjBjb2c4UXBIVTNEQWxYMVF6bzJtWFE9PSIsInZhbHVlIjoiV0l5NkdIZFBOSVN2YW53Q0lwVWNJUT09IiwibWFjIjoiMTM3YTlhZDE0N2U4ZTU2OGYxZTc2NDgzYTM1MThjNjk4OGM0ZjE1ZTlkY2U2YjU1MWQxOTQ2Y2QwYTFhZDMyMyIsInRhZyI6IiJ9',
                'sel_status' => true,
                'sel_uni_id' => 31,
                'created_at' => '2025-04-28 08:07:24',
                'updated_at' => '2025-05-02 14:08:11',
                'sel_dvr_ip' => 'eyJpdiI6Imc0cG95WkswRWVISHhkc0ZYd1N4Y3c9PSIsInZhbHVlIjoia1FoYS9BYzJ5RjNZalpDOFcvWG02dz09IiwibWFjIjoiMzRmMjJmMTA0NDI1MWUzYWVhZDBkMmU5MTZhMzNkNDA1ZTM4OTM5MzNmYTM1ZDY0ZjNmNzIyNWEwY2M5ZDRiMyIsInRhZyI6IiJ9',
                'sel_dvr_username' => 'eyJpdiI6IkhmSUpVdGR2c1RXczdmOGdkdlE3R2c9PSIsInZhbHVlIjoiUVZQUVZHaFVVM00vdDhqakpDTkNOQT09IiwibWFjIjoiMTY1ZjAxOWRlNDg1NWEzOTViZDBkNmUyYzk2YjAwMjA2OTkxM2Q0NTQ3ZDNhOGI1MzAxOTkyNGUyMDQ1NjAwMSIsInRhZyI6IiJ9',
                'sel_dvr_password' => 'eyJpdiI6IlFtTzhDdi9EM1N6NXNkS1o1UDBvaGc9PSIsInZhbHVlIjoicVN2SUVWUlZXaXludWFReEdOWSthQT09IiwibWFjIjoiZWRkMTM5MTVlNzBkYmJmMjZkNzViZmU2MDNkNWRkNjA0NDBlMTg4MTVkNjgzMThiNmUwOTM0MzQzM2M3YTYwMyIsInRhZyI6IiJ9',
                'sel_camera_canal' => '3',
                'sel_dvr_porta' => 554,
                'sel_rtsp_path' => 'eyJpdiI6IlZ4M3lDQWRlU1A4OTVUQTZEdHdPTFE9PSIsInZhbHVlIjoiVTIvNnNaaW94VVFrbVlQMEJwUlVmU2kvb2FIOGhJMEpXcVN5RnRsbEpKVS9qbkp2YklzWUtQTW8va1JvNlVVVW1zMDMyYVJTVW5LZUM2d3pDVytxZHhsbG9Sa3B2ampLcWFwQlg4c2lYM2h3UW1vME1ObG9Cd2RpU0grZFRZdm8iLCJtYWMiOiI5ODgzMTgwMTg2M2VjMGU3YjBmZGFhZmY0YTA1ZTBkZmUyM2Y0Njc2ZDlkN2E3MWJiNmZiZGYyOWM0NDBkOWI4IiwidGFnIjoiIn0=',
                'sel_pdv_codigo' => '216',
            ),
            2 => 
            array (
                'sel_id' => 44,
                'sel_name' => 'PDV 05',
                'sel_pdv_ip' => 'eyJpdiI6Ikt5YVZPNDhFY2w0KzJUMFVLQ0FFWnc9PSIsInZhbHVlIjoiUld5eFppOFdhNU82NUF0YldoVmk5QT09IiwibWFjIjoiNDY1YWY1ZGEzMzIzYjgwZTNlZTJlZGJmODMwNTdiNzFmYWI2MTRlYjVmMGJkYjBiMDI0M2Y3ZDNmNTMwZGQyOCIsInRhZyI6IiJ9',
                'sel_status' => true,
                'sel_uni_id' => 31,
                'created_at' => '2025-04-28 08:06:43',
                'updated_at' => '2025-05-02 16:17:08',
                'sel_dvr_ip' => 'eyJpdiI6IjFlaGFaTVg1ZFlVdHVLT3NobVRIMmc9PSIsInZhbHVlIjoiQTdyaldwWnNqTjZtUE1SRG44QmpVQT09IiwibWFjIjoiMTE0ZmQ3OTM3NzUzNjIzMGMwMTFmNjMxMDcwZjliMGViYjQyOWU3Y2IyNmZjMTc1NDk3NjlmNWZkZjU2YTExMCIsInRhZyI6IiJ9',
                'sel_dvr_username' => 'eyJpdiI6IndXdCtpZWdzSS80L2xaU1dxZWJoenc9PSIsInZhbHVlIjoiNCt2em9xS0t4d1lQWHJTZEFaai9yQT09IiwibWFjIjoiNDY3N2M2ZTU4NzU5YWQzY2ZjYmJkNWJiYTI0MzkxYWRjNDFkYWU5ZmMzYzlhMTU4YjU2ZGQ1ZmY3NzJhZDA3NiIsInRhZyI6IiJ9',
                'sel_dvr_password' => 'eyJpdiI6IkJDMkdidU54OVhEZVo5dkZTa0phenc9PSIsInZhbHVlIjoiY0JKbmVVMWpsc2lJWCtjUUg3emhQQT09IiwibWFjIjoiNTRmNmI5NDZhMDZiYTc1Njk2MjMyM2VhNDgyMDg0YjdkZjUxNjU2Y2E4NmMxOTk4NmJlODM0ODAwMzJkNDJkZCIsInRhZyI6IiJ9',
                'sel_camera_canal' => '2',
                'sel_dvr_porta' => 554,
                'sel_rtsp_path' => 'eyJpdiI6IjRacFZ1NThEbmhzbDUxL3dZcVpKYmc9PSIsInZhbHVlIjoiV3JVR3Y3SU9TSmxvaGxKaC95T25pWjFYZ01NWnFTYkZYTTlRZTJNNUNjdjZQU3hVb0J1Y2xacWthdXZTbEk4RW1pQjdtUjdlYlhQYWgyeGRmeHRvdk91NWU4U0tMUXpELzVZcTlwbUpJM2Q4S3NBK1d1V0FHNE5hSWs3dENnNUEiLCJtYWMiOiJhMGFiNTk0ZDdiZmFmNjk3NzMzZjBhYWI2YTg4OTFjOWNhOWIzNzlhMzZmYTgzZTI0NmYxM2E0MGZjOGU3ZmIxIiwidGFnIjoiIn0=',
                'sel_pdv_codigo' => '205',
            ),
            3 => 
            array (
                'sel_id' => 43,
                'sel_name' => 'PDV 01',
                'sel_pdv_ip' => 'eyJpdiI6IkhmSE9ETGp1Y21mdUVvZHpUdC9FN1E9PSIsInZhbHVlIjoidU5mZkhEQVNFVjFWbWcwaldlMnJaUT09IiwibWFjIjoiZTFmYjNmYmMwMjI3MTA1ZTA4ZDgzYmUyOTdiZDA2OTc5ZjJhMGE4NjI4MzRhNDkxODA0MThmMTA2NGNlZjYxNSIsInRhZyI6IiJ9',
                'sel_status' => true,
                'sel_uni_id' => 31,
                'created_at' => '2025-04-28 08:05:28',
                'updated_at' => '2025-04-30 14:37:07',
                'sel_dvr_ip' => 'eyJpdiI6ImJQNWZXZDZja0x3UWFMREtHVW9IM2c9PSIsInZhbHVlIjoiNFVWS1haVG5RWG9oVGR4SmhSSnJhUT09IiwibWFjIjoiODkzNGE3NmU0OGVjNzdmNTk1YmUwNDlhYzk5NmEzZWI3Nzc1NTYzOWRiNDcwZDRkNTY0YzJkMWZjOWZiNjAyNCIsInRhZyI6IiJ9',
                'sel_dvr_username' => 'eyJpdiI6IlV5dGI5aHBKMXAxcXh0R1ZRWVNCVGc9PSIsInZhbHVlIjoiSS9maFJzODRWWUhWWXluU0R4cm51QT09IiwibWFjIjoiZTdlYzRlN2E1N2FlNWI4MWE1YzdkYTFmNzY5MWVkMTE3YTYwZmM4MGZlOWM2YWY2YWNiYmRjYjg4YWExMjY3OCIsInRhZyI6IiJ9',
                'sel_dvr_password' => 'eyJpdiI6ImpBKytDczZOazFUMGdhQk0rbDlQcFE9PSIsInZhbHVlIjoid2RVYzd0WVkxam8veGN5QllxM2xKUT09IiwibWFjIjoiMzE1OTJlZjU2MTk0OWFhOWVlNjViNGE4NjBlYTlhZjEzYjA5MzRmZjI4ZmRkYjNjMDQzOWNlNTdjZDY2MWU3NSIsInRhZyI6IiJ9',
                'sel_camera_canal' => '1',
                'sel_dvr_porta' => 554,
                'sel_rtsp_path' => 'eyJpdiI6Im1zQVhSeFpHaEJYTml5YUk0N1R0cFE9PSIsInZhbHVlIjoidGtVME5EYXdzVkhrMmt6d1FGbEpRYzhVZ3IzZ0Y2ZDhyVlRFd1ZobGVsN3V3YTk4WlpBdDd0aHNpR3hRMTMwM3NsWkhQaml1Z2ZKZjVTaWk2dDkvaHB0M3lraE81cDBrRmk3MGVPdnZEaEljdmV0T2JndlFhNGNzT3ZRTGllOS8iLCJtYWMiOiJmYmJkNDAzZmVlOGYwMGJhMjgyYjVlZmVlZGE0YmFlZjFjMWYyY2FjNzlmM2YzNTlmOGQ2ZWNkYTBlYzg2YjNiIiwidGFnIjoiIn0=',
                'sel_pdv_codigo' => '201',
            ),
        ));
        
        
    }
}