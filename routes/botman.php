<?php

use App\Todo;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('/start', function ($bot) {
    $bot->reply('👋 Hi! I am the Build A Chatbot TodoBot!');
    $bot->reply('You can use "add new todo" to add new todos.');
});

$botman->hears('show my todos', function ($bot) {

    $todos = Todo::where('completed', false)
        ->where('user_id', $bot->getMessage()->getSender())
        ->get();

    if (count($todos) > 0) {

        $bot->reply('Your todos are:');

        foreach ($todos as $todo) {

            $bot->reply($todo->id.' - '.$todo->task);

        }

    } else {

        $bot->reply('You do not have any todos');

    }
});

$botman->hears('add new todo {task}', function ($bot, $task) {

    Todo::create([
        'task' => $task,
        'user_id' => $bot->getMessage()->getSender()
    ]);

    $bot->reply('You added a new todo for "'.$task.'"');

});

$botman->hears('add new todo', function ($bot) {

    $bot->ask('Which task do you want to add?', function ($answer, $conversation) {

        Todo::create([
            'task' => $answer,
            'user_id' => $conversation->getBot()->getMessage()->getSender()
        ]);

        $conversation->say('You added a new todo for "'.$answer.'"');
        
    });
    
});

$botman->hears('finish todo {id}', function ($bot, $id) {

    $todo = Todo::find($id);

    if (is_null($todo)) {

        $bot->reply('Sorry, I could not find a todo with ID "'.$id.'"');

    } else {

        $todo->completed = true;
        $todo->save();

        $bot->reply('Woohoo! You\'ve finished "'.$todo->task.'"!');

    }

});

$botman->hears('delete todo {id}', function ($bot, $id) {

    $todo = Todo::find($id);

    if (is_null($todo)) {

        $bot->reply('Sorry, I could not find a todo with ID "'.$id.'"');

    } else {

        $todo->delete();

        $bot->reply('You successfully deleted todo "'.$todo->task.'"!');

    }

});