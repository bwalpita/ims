<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo $page_title; ?>
                <a href = "<?php echo site_url('admin/users_payments'); ?>" class="btn btn-outline-primary btn-rounded alignToTitle"><i class="mdi mdi-arrow-left-bold"></i><?php echo get_phrase('Back'); ?></a>
            </h4>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">

                <h4 class="header-title mb-3"><?php echo get_phrase('generate_invoice'); ?></h4>

                <form class="required-form" action="<?php echo site_url('admin/payments/invoice/' . $payment->id); ?>" enctype="multipart/form-data" method="post">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row mb-3">
                                <label class="col-md-3 col-form-label" for="invoice">Action</label>
                                <div class="col-md-9">
                                    <button type="submit" class="col-md-3 btn btn-primary form-control" name="button"><?php echo get_phrase('generate'); ?></button>
                                </div>
                            </div>
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </form>

            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div>
</div>
