<div class="modal fade" id="add-link-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog {{ Helpers::hasAccess('super') ? 'modal-lg' : '' }}">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">{{ trans('stream::main.addLinkToTitle') }}</h4>
      </div>
      <div class="modal-body">
        @include('Titles.Create.Tabs.Panels')
      </div>
    </div>
  </div>
</div>