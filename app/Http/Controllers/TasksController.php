<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //もしログインしていたら一覧を表示する（ログインしている人が登録したタスクの一覧）
        if (\Auth::check()) {
            $user = \Auth::user();
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            return view('tasks.index', [
                'tasks' => $tasks,
             ]);
        }
        
        //ログインしていなければwlecomeブレードのビューを返す
        return view('welcome');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
          $task = new Task;

        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'status' => 'required|max:10',
            'content' => 'required|max:191',
        ]);
        $task = new Task;
        $task->content = $request->content;
        $task->status = $request->status;
        $task->user_id = \Auth::id(); 
        //ユーザーIDを$taskのプロパティに設定する。
        //ユーザーIDの取得方法　\Auth::id()の戻り値は現在ログイン中のユーザーIDです。
        $task->save();

        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function show($id)
    {
        $task = \App\Task::find($id);
        if (\Auth::id() === $task->user_id) {
            return view('tasks.show' , [
                         'task' => $task,
                ]); 
        }
        return redirect('/'); 
    }
    
    
    
    
    
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
       $task = \App\Task::find($id);
        if (\Auth::id() === $task->user_id) {
            return view('tasks.edit' , [ // viewはtasks.editですね。
                         'task' => $task,
                ]); 
        }
        return redirect('/'); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $task = \App\Task::find($id);
        if (\Auth::id() === $task->user_id) {
            // 更新処理
            $this->validate($request, [
                'status' => 'required|max:10',
                'content' => 'required|max:191',
            ]);
            
            $task->content = $request->content;
            $task->status = $request->status;
            $task->save();
        }
        return redirect('/');
        
    }
    
    
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = \App\Task::find($id);
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }
        return redirect('/');
    }
}
 