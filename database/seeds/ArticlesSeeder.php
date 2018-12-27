<?php

use Illuminate\Database\Seeder;

class ArticlesSeeder extends Seeder
{
    private $articles = [
        ['name' => 'АНГЕЛСКИ КРИЛА БЕЛИ 60 СМ', 'description' => 'Бели ангелски крила от имитация на пера, обточени с бял пух от горнатана страна.
Размер: 60 х 45 см', 'imageName' => 'krila.jpg'],
        ['name' => 'КОЛЕДНИ АКСЕСОАРИ ЗА ДОМАШЕН ЛЮБИМЕЦ', 'description' => 'Коледна шапка и яка на ластик, със звънчета, за вашия домашен любимец.
Материал: плюш', 'imageName' => 'pet.jpg'],
        ['name' => 'ОЧИЛА MERRY CHRISTMAS', 'description' => 'Страхотни, блестящи очила, без стъкла, подходящи за Коледно и Новогодишно парти.
Предлагат се в златен, сребърен и червен цвят.', 'imageName' => 'ochila.jpg'],
        ['name' => 'ОБУВКИ ЕЛФ', 'description' => 'Платнени обувки на Елф или джудже- наполовина червени, наполовина зелени, със звънче.
Дължина на подметката: 28- 31 см', 'imageName' => 'obyvki.jpeg'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->articles as $article) {
        DB::table('article')->insert([
            'name' => $article['name'],
           'description' => $article['description'],
            'imageName' => $article['imageName']
        ]);
    }
    }
}
