<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;



class CourseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Course';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Course());

        $grid->column('id', __('Id'));
        $grid->column('user_token', __('Teacher'))->display(function ($token) {
            return User::where('token', '=', $token)->value('name');
        });
        $grid->column('name', __('Name'));
        $grid->column('thumbnail', __('Thumbnail'))->image('', 50, 50);

        $grid->column('description', __('Description'));
        $grid->column('type_id', __('Type id'));
        $grid->column('price', __('Price'));
        $grid->column('lesson_num', __('Lesson num'));
        $grid->column('video_length', __('Video length'));

        $grid->column('downloadable_resources', __('Downloadable Resources'));
        $grid->column('created_at', __('Created at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Course::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('description', __('Description'));
        $show->field('price', __('Price'));
        $show->field('lesson_num', __('Lesson num'));
        $show->field('video_length', __('Video length'));
        $show->field('follow', __('Follow'));
        $show->field('score', __('Score'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('downloadable_resources', __('Downloadable resources'));

        return $show;
    }
    protected function form()
    {
        $form = new Form(new Course());
        $form->text('name', __('Name'));
        // it works wth key value pair
        // last one is key
        // id is key her
        $result = CourseType::pluck('title', 'id');
        // select method helps to select one of the options
        $form->select('type_id', __('Category'))->options($result);
        // dd($result);
        // $form->select('parent_id', __('Parent Category'))->options((new CourseType())::selectOptions());
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->file('video', __('Video'))->uniqueName();
        $form->text('description', __('Description'));
        $form->decimal('price', __('Price'));
        $form->number('lesson_num', __('Lesson Number'));
        $form->number('video_length', __('Video Length'));
        $form->number('downloadable_resources', __('Resources Num'));

        // who is posting
        $result = User::pluck('name', 'token');
        $form->select('user_token', __('Teacher'))->options($result);
        $form->display('created_at', __('Created at'));
        $form->display('updated_at', __('Updated at'));
        return $form;
    }
}