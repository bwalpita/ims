<!-- start page title -->
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('edit_attendance'); ?></h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row justify-content-center">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-body">
              <div class="col-lg-12">
                <h4 class="mb-3 header-title"><?php echo get_phrase('attendance_edit_form'); ?></h4>

                <form class="required-form" action="<?php echo site_url('admin/attendance/edit'); ?>" method="post" enctype="multipart/form-data">

                    <div class="form-group">
                        <label for="student"><?php echo get_phrase('student'); ?><span class="required">*</span></label>
                        <select class="form-control select2" data-toggle="select2" name="userId" id="student">
                          <?php foreach ($students as $student): ?>
                                <?php
                                    $selected = ($attendance['userId'] == $student['id']) ? "selected" : "";
                                ?>
                                <option value="<?php echo $student['id']; ?>" <?php echo $selected; ?>><?php echo $student['first_name'].' '.$student['last_name']; ?></option>
                          <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date"><?php echo get_phrase('date'); ?><span class="required">*</span></label>
                        <input type="date" class="form-control" id="date" name = "date_entered" value="<?php echo $attendance['date_entered']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="time"><?php echo get_phrase('time'); ?><span class="required">*</span></label>
                        <input type="time" class="form-control" id="time" name = "time" value="<?php echo $attendance['time']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status"><?php echo get_phrase('status'); ?><span class="required">*</span></label>
                        <select class="form-control select2" data-toggle="select2" name="status" id="status">
                            <option value="IN" <?php echo ($attendance['status'] == "IN") ? "selected" : ""; ?>>IN</option>
                            <option value="OUT" <?php echo ($attendance['status'] == "OUT") ? "selected" : ""; ?>>OUT</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="checkRequiredFields()"><?php echo get_phrase("submit"); ?></button>
                </form>
              </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<script type="text/javascript">
    function checkCategoryType(category_type) {
        if (category_type > 0) {
            $('#thumbnail-picker-area').hide();
            $('#icon-picker-area').hide();
        }else {
            $('#thumbnail-picker-area').show();
            $('#icon-picker-area').show();
        }
    }
</script>
