
    </div>
</div>
@if (session('success'))
<div class="toast align-items-center text-bg-success border-0 position-fixed show end-0 top-0 m-3" 
     role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999;">
  <div class="d-flex">
    <div class="toast-body">
      {{ session('success') }}
    </div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" 
            data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>
@endif

@if (session('error'))
<div class="toast align-items-center text-bg-danger border-0 position-fixed show end-0 top-0 m-3" 
     role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999;">
  <div class="d-flex">
    <div class="toast-body">
      {{ session('error') }}
    </div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" 
            data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>
@endif
    <script src="{{asset('js/bootstrap.bundle.js')}}"></script>
    <script src="{{asset('js/script.js')}}"></script>
    <script src="{{asset('js/jquery.js')}}"></script>
    <script src="{{asset('js/datatable.js')}}"></script>
    <script src="{{asset('js/choices.js')}}"></script>
    <script src="{{asset('js/flatpickr.js')}}"></script>
    <script src="{{asset('js/select2.js')}}"></script>

    <x-delete-modal />
</body>

<script>
    document.querySelectorAll('.choices').forEach(function(el){
        new Choices(el, {
            removeItemButton: true,
            searchEnabled: true
        });
    });

    flatpickr(".date", {
        dateFormat: "d-m-Y"
    });
    
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>

</html>
<style>
    .dataTables_filter{
        margin-bottom: 10px !important;
    }
    .choices__inner{
        padding: 5px !important;
        border-radius: 5px !important;
        min-height: 40px !important;
        background-color: transparent !important;
    }
    .choices__list.choices__list--dropdown{
        border-radius: 5px !important;
        margin-top: 5px !important;
    }
</style>