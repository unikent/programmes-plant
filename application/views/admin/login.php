<?php echo View::make('admin.inc.meta')->render();?>
    <title>Login &lt; Courses Dashboard</title>
  </head>
  <body>
    <div class="container loginwindow">

          <h1>Login To Your Dashboard</h1>

          
          <?php 

          if(Session::has('flash')){
             echo '<div class="alert">'.Session::get('flash').'</div>';

          }
          ?>


          <?php echo Form::open('login', 'POST',array('class'=>'form-inline'));?>
          <?php echo Form::token()?>
            <div class="control-group">
              <label class="control-label" for="username">Username</label>
              <div class="controls">
                <input type="text" class="input-xlarge" id="username" name="username" placeholder="Enter Your Username...">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="password">Password</label>
              <div class="controls">
                <input type="password" class="input-xlarge" id="password" name="password" placeholder="Enter Your Password...">
              </div>
            </div>
            <input type="submit" class="btn btn-primary" value="Login" />
          <?php echo Form::close()?>
    </div> <!-- /container -->

    <?php echo View::make('admin.inc.scripts')->render()?>
  </body>
</html>
