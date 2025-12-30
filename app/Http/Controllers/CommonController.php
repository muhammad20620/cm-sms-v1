<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Session;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Enrollment;
use Illuminate\Support\Str;
use App\Models\Gradebook;
use App\Models\Subject;
use App\Models\School;
use App\Models\Exam;
use DB;
use PDF;



class CommonController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function get_student_details_by_id($id = "", $api="")
    {

        //Fetch Details

        $enrol_data = Enrollment::where('user_id', $id)->first();

        $student = User::find($id);

        $info = json_decode($student->user_information);

        $parent_details = User::find($student->parent_id);

        $role = Role::where('role_id', $student->role_id)->first();

        $class_details = Classes::find($enrol_data->class_id);

        $section_details = Section::find($enrol_data->section_id);

        $active_session = get_school_settings($student->school_id)->value('running_session');

        $school_name = School::find($student->school_id)->value('title');

        //End Fetch


        $enrol_data['code'] = $student->code;
        $enrol_data['user_id'] = $id;
        $enrol_data['parent_name'] = $parent_details->name??"";
        $enrol_data['name'] = $student->name;
        $enrol_data['email'] = $student->email;

        $enrol_data['role'] = $role->name;

        $enrol_data['address'] = $info->address;
        $enrol_data['phone'] = $info->phone;
        $enrol_data['birthday'] = $info->birthday;
        $enrol_data['gender'] = $info->gender;
        $enrol_data['blood_group'] = $info->blood_group??"";
        $enrol_data['student_info']    = $student->student_info;
        $enrol_data['photo'] = isset($api) && $api == 'api' ? get_user_image_url($id): get_user_image($id);
        $enrol_data['school_id'] = $student->school_id;
        $enrol_data['school_name'] = $school_name;
        $enrol_data['running_session'] = $active_session;

        $enrol_data['class_name'] = $class_details->name ??"";
        $enrol_data['class_id'] = $class_details->id ??"";
        $enrol_data['section_name'] = $section_details->name ??"";
        $enrol_data['section_id'] = $section_details->id ??"";

        return $enrol_data;
    }

    public function getAdminDetails($id)
    {
        // $admin_details = User::where('school_id', auth()->user()->school_id);

        $user_details = User::find($id);

        $info = json_decode($user_details->user_information);
        
        $user_data['id'] = $user_details->id;
        $user_data['name'] = $user_details->name;
        $user_data['email'] = $user_details->email;

        $user_data['address'] = $info->address;
        $user_data['phone'] = $info->phone;
        $user_data['birthday'] = $info->birthday;
        $user_data['gender'] = $info->gender;
        $user_data['blood_group'] = $info->blood_group??"";
        $user_data['photo'] = get_user_image($id);
        $user_data['school_id'] = $user_details->school_id;

        return $user_data;
    }


    public function get_student_academic_info($id = "")
    {

        //Fetch Details
        $enrol_data = Enrollment::where('user_id', $id)->first();
        $student = User::find($id);
        $class_details = Classes::find($enrol_data->class_id);

        $section_details = Section::find($enrol_data->section_id);

        //End Fetch

        $enrol_data['parent_id'] = $student->parent_id;
        $enrol_data['code'] = $student->code;
        $enrol_data['user_id'] = $id;
        $enrol_data['name'] = $student->name;
        $enrol_data['email'] = $student->email;


        $enrol_data['class_name'] = $class_details->name ??"";
        $enrol_data['class_id'] = $class_details->id ??"";
        $enrol_data['section_name'] = $section_details->name ??"";
        $enrol_data['section_id'] = $section_details->id ??"";

        return $enrol_data;
    }



    public function classWiseStudents($id = '')
    {
        $enrollments = Enrollment::get()->where('class_id', $id);
        $options = '<option value="">' . 'Select a student' . '</option>';
        foreach ($enrollments as $enrollment) :
            $student = User::find($enrollment->user_id);
            $options .= '<option value="' . $student->id . '">' . $student->name . '</option>';
        endforeach;
        echo $options;
    }

    public function classWiseSubject($id)
    {
        $subjects = Subject::get()->where('class_id', $id);
        $options = '<option value="">' . 'Select a subject' . '</option>';
        foreach ($subjects as $subject) :
            $options .= '<option value="' . $subject->id . '">' . $subject->name . '</option>';
        endforeach;
        echo $options;
        // return view('admin.examination.add_offline_exam', ['subjects' => $subjects]);
    }

    public function classWiseSections($id)
    {
        $sections = Section::get()->where('class_id', $id);
        $options = '<option value="">' . 'Select a section' . '</option>';
        foreach ($sections as $section) :
            $options .= '<option value="' . $section->id . '">' . $section->name . '</option>';
        endforeach;
        echo $options;
    }

    public function sectionWiseStudents($id)
    {
        $enrollments = Enrollment::where('section_id', $id)->where('school_id', auth()->user()->school_id)->get();
        $options = '<option value="">' . 'Select a student' . '</option>';
        foreach ($enrollments as $enrollment) :
            $student = User::find($enrollment->user_id);
            $options .= '<option value="' . $student->id . '">' . $student->name . '</option>';
        endforeach;
        echo $options;
    }

    public function studentWiseParent($id)
    {
        $student = User::find($id);
        
        $parent_details = User::find($student->parent_id);

        $options = '<option value="">' . 'Select Parent' . '</option>';
        
            $options .= '<option value="' . $parent_details['id'] . '">' . $parent_details['name'] . '</option>';
        
        echo $options;
    }


    public function getGrade($acquired_mark = '')
    {
        $total_marks = request()->query('total_marks');
        if ($total_marks !== null && $total_marks !== '') {
            echo get_grade_for_total_marks($acquired_mark, $total_marks);
            return;
        }
        echo get_grade($acquired_mark);
    }

    public function markUpdate(Request $request)
    {
        $data = $request->all();

        // Basic validation
        $markRaw = $data['mark'] ?? null;
        if ($markRaw === null || $markRaw === '') {
            return response()->json(['status' => 'error', 'message' => 'Mark is required.'], 422);
        }
        if (!is_numeric($markRaw) || (float) $markRaw < 0) {
            return response()->json(['status' => 'error', 'message' => 'Mark must be a number greater than or equal to 0.'], 422);
        }

        $subjectId = $data['subject_id'] ?? null;
        if ($subjectId === null || $subjectId === '') {
            return response()->json(['status' => 'error', 'message' => 'Subject is required.'], 422);
        }

        if (!empty($data['session_id'])) {
            $active_session  = $data['session_id'];
        } else {
            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        }

        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        // Enforce max marks based on the configured offline exam
        $exam = Exam::where('exam_type', 'offline')
            ->where('class_id', $data['class_id'] ?? null)
            ->where('subject_id', $subjectId)
            ->where('session_id', $data['session_id'])
            ->where('exam_category_id', $data['exam_category_id'] ?? null)
            ->where('school_id', $data['school_id'])
            ->first();

        if ($exam && is_numeric($exam->total_marks) && (float) $exam->total_marks > 0) {
            $max = (float) $exam->total_marks;
            if ((float) $markRaw > $max) {
                return response()->json(['status' => 'error', 'message' => "Mark cannot be greater than {$max}."], 422);
            }
        }

        $query = Gradebook::where('exam_category_id', $data['exam_category_id'])
            ->where('class_id', $data['class_id'])
            ->where('section_id', $data['section_id'])
            ->where('student_id', $data['student_id'])
            ->where('school_id', $data['school_id'])
            ->where('session_id', $data['session_id'])
            ->first();

        if (!empty($query)) {

            $marks = json_decode((string) $query->marks, true);
            $marks = is_array($marks) ? $marks : [];
            $marks[$subjectId] = $markRaw;
            $query->marks = json_encode($marks);
            $query->comment = $data['comment'];
            $query->save();


        } else {
            $mark[$subjectId] = $markRaw;
            $marks = json_encode($mark);
            $data['marks'] = $marks;
            $data['timestamp'] = strtotime(date('Y-m-d'));
            Gradebook::create($data);
        }

        return response()->json(['status' => 'success']);
    }

    public function get_user_by_id_from_user_table($id)
    {
        $user = User::find($id);

        return $user;
    }

    public function idWiseUserName($id='')
    {
        $result = User::where('id', $id)->value('name');
        return $result;
    }

    public function getClassDetails($id='')
    {
        $class_details = Classes::find($id);
        return $class_details;
    }

    public function getSectionDetails($id='')
    {
        $section_details = Section::find($id);
        return $section_details;
    }

    public function getSubjectDetails($id='')
    {
        $subject_details = Subject::find($id);
        return $subject_details;
    }

   

  

}
