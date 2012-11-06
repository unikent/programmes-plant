<?php echo View::make('admin.inc.meta')->render();?>
    <title>Courses Dashboard</title>
    <style type="text/css">
.mainlist  {margin-left:0px;}
  .mainlist li {
    
    font-size:2em;
    list-style:none;
    
    
  }
  .mainlist li a {
     color:#000;
     background-color:#FBFBFB;
     display:block;
     padding:10px;
      border: 1px solid #DDD;
  }
  .mainlist li ul{
    padding:5px 0;
    margin:0 8px;
  }
  .mainlist li ul li {
    font-size:0.5em;
    padding:5px;
    background-color: transparent;
  }
  .mainlist li ul li a {
    padding:5px;
    background-color: transparent;
    border: hidden;



  }
  .btn-custom-width {

    width:180px;
    text-align:left;
  }

</style>
  </head>
  <body>
    <?php echo View::make('admin.inc.header')->render()?>
    <div class="container">
   

      <div class="row-fluid">

        <div class="span3"> <!-- Sidebar -->
          <div class="well">
            <?php echo View::make('admin.inc.sidebar')->render()?>
          </div>
        </div> <!-- /Sidebar -->

        <div class="span9">
         
          <div>

   <form class="right form-inline">
       <input type="text" class="input-large search-query " placeholder="Filter">
  </form>
<div class="page-header" style="height:30px">


  <div  style='float:left;width:60px;font-weight:bold;padding:5px'>Display:</div>
  <div class="btn-group left" style='float:left;'>

        <a class="btn " href="#">

          <i class="icon-calendar "></i>Undergraduate</a>
         
          <a class="btn  dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">Postgraduate</a></li>
          </ul>
        </div>
         <div class="btn-group left"  style='float:left;'>
          <a class="btn " href="#">

          <i class="icon-calendar "></i> <?php echo URI::segment(1);?></a>
         
          <a class="btn  dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">2011</a></li>
            <li><a href="#">2012</a></li>
            <li><a href="#">2013</a></li>
            <li><a href="#">2014</a></li>
          </ul>
        </div>


 
</div>
</div>
<ul class='mainlist'>
<?php $url = URL::current(); ?>
<?php foreach ($programmes as $programme) : ?>
  <li><a href='<?php echo $url;?>/programmes/edit/<?php echo $programme->id; ?>'><?php echo $programme->title; ?></a></li>
<?php endforeach; ?>
</ul>
        </div>

      </div>
      <div class="row-fluid">
        <div class="span12">
          <p>You are logged in as: <?php echo Auth::user(); ?></p>
        </div>
      </div>
    </div> <!-- /container -->

    <?php echo View::make('admin.inc.scripts')->render()?>
  </body>
</html>
