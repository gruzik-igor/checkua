<div class="container">
    <div class="row">
        <div class="col-lg-6">
                <div class="form-group">
                    <label for="name">Ім'я та прізвище</label>
                    <input type="text" class="form-control" name="name" id="userName" value="<?= $_SESSION['user']->name?>">
                </div>
                <?php
                    $this->load->library('form');
                    $this->form->showForm('user_info');
                ?>
        </div>
    </div>
</div>