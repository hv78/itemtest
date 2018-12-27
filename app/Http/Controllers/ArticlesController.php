<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ArticlesController extends Controller
{
    private function rules($id)
    {
        return [
            'name' => 'required|max:50|unique:article,name,' . $id,
            'description' => 'required|max:500',
            'imageName' => 'required|image|mimes:jpeg,png,jpg,gif,svg,bmp|max:2048'
        ];
    }

    private function rule()
    {
        return [
            'name' => 'required|max:50|unique:article,name,',
            'description' => 'required|max:500',
            'imageName' => 'required|image|mimes:jpeg,png,jpg,gif,svg,bmp|max:2048'
        ];
    }

    private $errors = [
        'name.required' => "Please fill the field.",
        'name.unique' => 'The name of the item is already used.',
        'name.max' => 'Max characters: 50.',
        'description.required' => "Please fill the field.",
        'description.max' => 'Max characters: 500.',
        'imageName.required' => 'Please upload image.',
        'imageName.image' => 'Please upload image.',
        'imageName.mimes' => 'Only jpeg, jpg, png, gif, bmp and svg types are allowed.',
        'imageName.max' => 'Max size is 2MB.',
    ];

    function index()
    {
       $articles = DB::table('article')->get();
        return Datatables::of($articles)->addColumn('action', function($article){
            return '<a href="#" class="btn btn-xs btn-primary edit" id="'.$article->id.'">
<i class="glyphicon glyphicon-edit"></i> Edit</a><a href="#" class="btn btn-xs btn-danger delete" id="'.$article->id.'">
<i class="glyphicon glyphicon-remove"></i> Delete</a>';
        })
            ->make(true);
    }

    public function store(Request $request)
    {
        $t = $request->get('article_id');
        $validator = Validator::make($request->all(), $this->rules($request->get('article_id')), $this->errors);
        $success_output = '';
        $validatorErrors = [];
        $validatorErrorsCount =0;
        if ($validator->fails())
        {
            foreach ($this->rule() as $key => $value)
            {
                $validatorErrors[$key] = $validator->errors()->first($key);

            }
         $validatorErrorsCount = count($validatorErrors);
        }
        else
        {
            if($request->get('button_action') == "insert")
            {
                if ($request->hasFile('imageName')) {
                    $image = $request->file('imageName');
                    $name = time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('/images');
                    $image->move($destinationPath, $name);

                    $article['name'] = $request->get('name');
                    $article['description'] = $request->get('description');
                    $article['imageName'] = $name;
                    DB::table('article')->insert($article);

                    $success_output = '<div class="alert alert-success">Data inserted successfully.</div>';
                }
            }

            if($request->get('button_action') == 'update')
            {
                if ($request->hasFile('imageName')) {
                    $image = $request->file('imageName');
                    $name = time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('/images');
                    $image->move($destinationPath, $name);



                    $article['name'] = $request->get('name');
                    $article['description'] = $request->get('description');
                    $article['imageName'] = $name;
                    DB::table('article')->where('id',$request->get('article_id'))->update($article);

                    $success_output = '<div class="alert alert-success">Data updated successfully.</div>';
                }
            }
        }
        $output = array(
            'successes'   =>  $success_output,
            'errors' => $validatorErrors,
            'errorsCount' => $validatorErrorsCount,
            'test' => $t
        );
        echo json_encode($output);
    }

    function fetchdata(Request $request)
    {
        $id = $request->input('id');
        $article = DB::table('article')->find($id);

        if (!is_null($article)) {
            $output = array(
                'name' => $article->name,
                'description' => $article->description,
                'imageName' =>  $article->imageName
            );
            echo json_encode($output);
        }
    }
}
