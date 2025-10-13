   
<?php $__env->startSection('content'); ?>
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4><?php echo e(get_phrase('Student Feedback')); ?></h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#"><?php echo e(get_phrase('Home')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Appraisal')); ?></a></li>
                        <li><a href="#"><?php echo e(get_phrase('Student Feedback')); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap-2">
            <div class="container mt-5">
                <?php
                    $groupedFeedbacks = [];
                    foreach ($feedbacks as $feedback) {
                        $answers = json_decode($feedback->answers, true);
                        foreach ($answers as $teacherId => $teacherAnswers) {
                            $groupedFeedbacks[$teacherId][$feedback->apprasial_id][] = [
                                'student_id' => $feedback->student_id,
                                'answers' => $teacherAnswers
                            ];
                        }
                    }
                ?>
                 <?php if(count($groupedFeedbacks) > 0): ?>
                <div class="accordion" id="teacherAccordion">
                    <?php $__currentLoopData = $groupedFeedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacherId => $appraisals): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $teacher = DB::table('users')->where('id', $teacherId)->first(); 
                            if ($teacher) {
                                $info = json_decode($teacher->user_information, true);
                                $user_image = !empty($info['photo']) ? 'uploads/user-images/'.$info['photo'] : 'uploads/user-images/thumbnail.png';
                                $teachers[] = [
                                    'id' => $teacherId,
                                    'name' => $teacher->name,
                                    'image' => $user_image
                                ];
                            }
                        ?>
                        <div class="accordion-item mb-3">
                            <div class="accordion-header">
                                <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#teacher<?php echo e($teacherId); ?>">
                                    <img class="img-fluid rounded-circle mx-3" width="50" height="50" src="<?php echo e(asset('assets/' . $user_image)); ?>" />   <?php echo e($teacher->name); ?>

                                </button>
                            </div>
                            <div id="teacher<?php echo e($teacherId); ?>" class="accordion-collapse collapse" data-bs-parent="#teacherAccordion">
                                <div class="accordion-body">
                                    <div class="accordion" id="appraisalAccordion<?php echo e($teacherId); ?>">
                                        <?php $__currentLoopData = $appraisals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appraisalId => $feedbacks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $appraisal = DB::table('appraisals')->find($appraisalId);
                                                $appraisalSummary = [];
                                                
                                                foreach ($feedbacks as $feedback) {
                                                    foreach ($feedback['answers'] as $index => $answer) {
                                                        $appraisalSummary[$index][$answer] = ($appraisalSummary[$index][$answer] ?? 0) + 1;
                                                    }
                                                }
                                            ?>
                                            <?php if(!empty($appraisal)): ?>
                                            <div class="accordion-item mb-3">
                                                <div class="accordion-header">
                                                    <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#appraisal<?php echo e($appraisalId); ?>">
                                                        <?php echo e($appraisal->title); ?>

                                                    </button>
                                                </div>
                                                <div id="appraisal<?php echo e($appraisalId); ?>" class="accordion-collapse collapse" data-bs-parent="#appraisalAccordion<?php echo e($teacherId); ?>">
                                                    <div class="accordion-body">
                                                       
                                                        <span>📊 <?php echo e(get_phrase('Answer Summary')); ?></span>
                                                            <?php if($appraisal->ans_type == 'mcq'): ?>
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th><?php echo e(get_phrase('Question')); ?></th>
                                                                        <th><?php echo e(get_phrase('Excellent')); ?></th>
                                                                        <th><?php echo e(get_phrase('Good')); ?></th>
                                                                        <th><?php echo e(get_phrase('Average')); ?></th>
                                                                        <th><?php echo e(get_phrase('Poor')); ?></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $appraisalSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $questionIndex => $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $questions = json_decode($appraisal->question, true);
                                                                        $questionText = $questions[$questionIndex] ?? 'Q'.($questionIndex+1);
                                                                    ?>
                                                                        <tr>
                                                                            <td style="max-width: 173px;"><?php echo e('Q'.($questionIndex+1).': '.$questionText); ?></td>
                                                                            <td><?php echo e($summary['Excellent'] ?? 0); ?></td>
                                                                            <td><?php echo e($summary['Good'] ?? 0); ?></td>
                                                                            <td><?php echo e($summary['Average'] ?? 0); ?></td>
                                                                            <td><?php echo e($summary['Poor'] ?? 0); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                        
                                                        <?php elseif($appraisal->ans_type == 'rating'): ?>
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th><?php echo e(get_phrase('Question')); ?></th>
                                                                        <th>⭐⭐⭐⭐⭐</th>
                                                                        <th>⭐⭐⭐⭐</th>
                                                                        <th>⭐⭐⭐</th>
                                                                        <th>⭐⭐</th>
                                                                        <th>⭐</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $appraisalSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $questionIndex => $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $questions = json_decode($appraisal->question, true);
                                                                        $questionText = $questions[$questionIndex] ?? 'Q'.($questionIndex+1);
                                                                    ?>
                                                                    <tr>
                                                                        <td style="max-width: 173px;"><?php echo e('Q'.($questionIndex+1).': '.$questionText); ?></td>
                                                                            <td><?php echo e($summary['5'] ?? 0); ?></td>
                                                                            <td><?php echo e($summary['4'] ?? 0); ?></td>
                                                                            <td><?php echo e($summary['3'] ?? 0); ?></td>
                                                                            <td><?php echo e($summary['2'] ?? 0); ?></td>
                                                                            <td><?php echo e($summary['1'] ?? 0); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                        
                                                        <?php elseif($appraisal->ans_type == 'binary'): ?>
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th><?php echo e(get_phrase('Question')); ?></th>
                                                                        <th><?php echo e(get_phrase('Yes')); ?></th>
                                                                        <th><?php echo e(get_phrase('No')); ?></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $__currentLoopData = $appraisalSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $questionIndex => $summary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $questions = json_decode($appraisal->question, true);
                                                                        $questionText = $questions[$questionIndex] ?? 'Q'.($questionIndex+1);
                                                                    ?>
                                                                    <tr>
                                                                        <td style="max-width: 173px;"><?php echo e('Q'.($questionIndex+1).': '.$questionText); ?></td>
                                                                            <td><?php echo e($summary['Yes'] ?? 0); ?></td>
                                                                            <td><?php echo e($summary['No'] ?? 0); ?></td>
                                                                        </tr>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </tbody>
                                                            </table>
                                                            <?php elseif($appraisal->ans_type == 'text'): ?>
                                                                <?php
                                                                    $questions = json_decode($appraisal->question, true);
                                                                ?>
                                                                <table class="table table-sm table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th><?php echo e(get_phrase('Question')); ?></th>
                                                                            <th><?php echo e(get_phrase('Student')); ?></th>
                                                                            <th><?php echo e(get_phrase('Answer')); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php $__currentLoopData = $feedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feedback): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php
                                                                                $student = DB::table('users')->where('id', $feedback['student_id'])->first();
                                                                            ?>
                                                                            <?php $__currentLoopData = $feedback['answers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <tr>
                                                                                    <td><?php echo e('Q'.($index+1).': '.($questions[$index] ?? '')); ?></td>
                                                                                    <td><?php echo e($student->name); ?></td>
                                                                                    <td><?php echo e($answer); ?></td>
                                                                                </tr>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </tbody>
                                                                </table>
                                                            <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="empty_box center">
                    <img class="mb-3" width="150px" src="<?php echo e(asset('assets/images/empty_box.png')); ?>" />
                    <br>
                    <span class=""><?php echo e(get_phrase('No data found')); ?></span>
                </div> 
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Ekattor8 School Management System/eq2/resources/views/admin/appraisal/studentFeedback.blade.php ENDPATH**/ ?>