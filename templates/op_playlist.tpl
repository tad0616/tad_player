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
      <select id="menu1" class="form-select" title="select category">
        <{$cate_select|default:''}>
      </select>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <{$title|default:''}>
    <{$playcode|default:''}>
  </div>
</div>

<div class="text-center" style="margin: 20px auto;">
  <{$push|default:''}>
</div>