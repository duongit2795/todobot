<?php

namespace Tests\BotMan;

use App\Todo;
use Tests\TestCase;

class TodobotTest extends TestCase
{
    /**
     * Tests the start message.
     *
     * @return void
     */
    public function testStart()
    {
        $this->bot
            ->receives('/start')
            ->assertReply(trans('bot.start_reply'))
            ->assertReply(trans('bot.start_reply_byline'));

        $this->bot
            ->receives('GET_STARTED')
            ->assertReply(trans('bot.start_reply'))
            ->assertReply(trans('bot.start_reply_byline'));
    }

    /**
     * Tests the create function without pre-defined todo task value.
     *
     * @return void
     */
    public function testAddNewTodo()
    {
        $this->bot
            ->receives('add new todo')
            ->assertReply('Which task do you want to add?')
            ->receives('Test Chatbot')
            ->assertReply('You added a new todo for "Test Chatbot"');

        $this->assertDatabaseHas('todos', [
            'task' => 'Test Chatbot'
        ]);
    }

    /**
     * Tests the create function with pre-defined todo task value.
     *
     * @return void
     */
    public function testAddNewTodoWithValue()
    {
        $this->bot
            ->receives('add new todo Test Chatbot')
            ->assertReply('You added a new todo for "Test Chatbot"');

        $this->assertDatabaseHas('todos', [
            'task' => 'Test Chatbot'
        ]);
    }

    /**
     * Tests that none existing todos cannot be finished.
     *
     * @return void
     */
    public function testCannotFinishNoneExistingTodo()
    {
        $this->bot
            ->receives('finish todo 9999999999999999')
            ->assertReply('Sorry, I could not find a todo with ID "9999999999999999"');
    }

    /**
     * Tests that todos can be finished.
     *
     * @return void
     */
    public function testCanFinishTodo()
    {
        $todo = Todo::create([
            'task' => 'Some test task',
            'user_id' => 1
        ]);

        $this->bot
            ->receives('finish todo '.$todo->id)
            ->assertReply('Woohoo! You\'ve finished "'.$todo->task.'"!');

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'completed' => true
        ]);
    }

    /**
     * Tests that none existing todos cannot be deleted.
     *
     * @return void
     */
    public function testCannotDeleteNoneExistingTodo()
    {
        $this->bot
            ->receives('delete todo 9999999999999999')
            ->assertReply('Sorry, I could not find a todo with ID "9999999999999999"');
    }

    /**
     * Tests that todos can be deleted.
     *
     * @return void
     */
    public function testCanDeleteTodo()
    {
        $todo = Todo::create([
            'task' => 'Some test task',
            'user_id' => 1
        ]);

        $this->bot
            ->receives('delete todo '.$todo->id)
            ->assertReply('You successfully deleted todo "'.$todo->task.'"!');

        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id
        ]);
    }
}
