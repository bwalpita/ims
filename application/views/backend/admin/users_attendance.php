<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo $page_title; ?></h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
              <h4 class="mb-3 header-title"><?php echo get_phrase('student attendance'); ?></h4>
              <div class="row justify-content-md-center">
            </div>
            <div class="table-responsive-sm mt-4">
                <table id="basic-datatable" class="table table-striped table-centered mb-0">
                  <thead>
                     <tr>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Course Name</th>
                      <th>Status</th>
                      <th>Time</th>
                  </tr>

              </thead>
              <tbody>
                <?php if (isset($attendances)): ?>
                  <?php foreach ($attendances as $attendance): ?>
                      <tr>
                          <td><?php echo $attendance['first_name'] ?></td>
                          <td><?php echo $attendance['last_name'] ?></td>
                          <td><?php echo $attendance['name'] ?></td>
                          <td><?php echo $attendance['status'] ?></td>
                          <td><?php echo $attendance['time'] ?></td>
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
