<?php

use Illuminate\Database\Seeder;
use App\Models\UserStatistics;

class DtuSeeder extends Seeder {

  public function run()
  {
    DB::table('user_statistics')->delete();

    for ($i=0; $i < 10; $i++) {
      UserStatistics::create([
        'date'   => date("Y-m").'-'.$i,
        'user_total'    => rand(10,1000), //数据不准确，随机生成
        'user_from_mobile'    => rand(10,1000),
        'user_from_wx' => rand(10,1000),
        'user_from_qq' => rand(10,1000),
        'user_from_weibo' => rand(10,1000),
        'user_from_wx' => rand(10,1000),
      ]);
    }
  }

}