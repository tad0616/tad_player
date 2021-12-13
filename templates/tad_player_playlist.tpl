<{$toolbar}>

<h2 class="sr-only visually-hidden">Video List</h2>

<script type="text/javascript">
  $(document).ready(function(){
    $("#menu1").change(function(){
      location.href="playlist.php?pcsn=" +　$("#menu1").val();
    });
  });
</script>

<div class="alert alert-success">
  <div class="row">
    <div class="col-sm-4">
      <select id="menu1" class="form-control" title="select category">
        <{$cate_select}>
      </select>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <{$title}>
    <{$playcode}>
  </div>
</div>

<div class="text-center" style="margin: 20px auto;">
  <{$push}>
</div>