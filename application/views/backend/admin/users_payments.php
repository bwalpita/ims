<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo $page_title; ?>
                <a href = "<?php echo site_url('admin/user_payment_form/add_user_payment_form'); ?>" class="btn btn-outline-primary btn-rounded alignToTitle"><i class="mdi mdi-plus"></i><?php echo get_phrase('Make Payment'); ?></a></h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
              <h4 class="mb-3 header-title"><?php echo get_phrase('student'); ?></h4>
              <div class="row justify-content-md-center">
                <div class="col-xl-6">
                    <form class="form-inline" action="<?php echo site_url('admin/users_payment/search') ?>" method="post">
                        <div class="col-xl-10">
                            <div class="form-group">
                                <select name="course_id" id="course_id" class="form-control">
                                    <?php foreach ($courses as $course): ?>
                                        <?php echo '<option value="'.$course['id'].'" selected>'.$course['title'].'</option>'; ?>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-2">
                            <button type="submit" class="btn btn-info" id="submit-button" > <?php echo get_phrase('filter');?></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive-sm mt-4">
                <table id="basic-datatable" class="table table-striped table-centered mb-0">
                  <thead>
                     <tr>
                      <th>Course Title</th>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Course Price</th>
                      <th>Paid Amount</th>
                      <th>Oustanding</th>
                  </tr>

              </thead>
              <tbody>
                <?php if (isset($users)): ?>
                  <?php foreach ($users as $user): ?>
                      <tr>
                          <td><?php echo $user['title'] ?></td>
                          <td><?php echo $user['first_name'] ?></td>
                          <td><?php echo $user['last_name'] ?></td>
                          <td><?php echo $user['price'] ?></td>
                          <td><?php echo $user['paid_amount'] ?></td>
                          <td><?php echo ($user['price'] - $user['paid_amount']) ?></td>
                      </tr>
                  <?php endforeach ?>
              <?php endif ?>
          </tbody>
      </table>
  </div>
</div> <!-- end card body-->
</div> <!-- end card -->
</div><!-- end col-->
</div>
