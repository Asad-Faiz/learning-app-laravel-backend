<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\CourseType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;



class CourseTypeController extends AdminController
{
    // actually t show tree form of the menus
    public function index(Content $content)
    {
        $tree = new Tree(new CourseType);
        return $content->header("Course Types")
            ->body($tree);
    }
    //
    protected function detail($id)
    {
        $show = new Show(CourseType::findorFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Category'));
        $show->field('description', __('Description'));
        $show->field('order', __('Order'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        /*$show->disableActions(); //disable action buttons on Admin pannel (update,delete)
        $show->disableCreateButton();
        $show->disableExport();
        $show->disableFilter();
        return $show;*/
    }
    protected function form()
    {
        $form = new Form(new CourseType());
        $form->select('parent_id', __('Parent Category'))->options((new CourseType())::selectOptions());
        $form->text('title', __('Title'));
        $form->textarea('description', __('Description'));
        $form->number('order', __('Order'));
        return $form;
    }
}