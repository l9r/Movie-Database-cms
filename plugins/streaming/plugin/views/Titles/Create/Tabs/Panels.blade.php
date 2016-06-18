<div class="tab-pane" id="links">
   <div class="clearfix">
        
        <div class="form-group">
            <label for="newLink">URL</label>
            <input type="text" name="newLink" placeholder="Url..." class="form-control" data-bind="value: app.models.link.url, valueUpdate: 'keyup'">
        </div>

        <div class="form-group">
            <label for="link-label">{{ trans('stream::main.label') }}</label>
        <input type="text" class="form-control" name="link-label" data-bind="value: app.models.link.label, valueUpdate: 'keyup'">
        </div>

        <div class="form-group">
        <label for="link-label">{{ trans('stream::main.quality') }}</label>
        	<input type="text" class="form-control" name="link-label" data-bind="value: app.models.link.quality, valueUpdate: 'keyup'">
        </div>

        <div class="form-group">
            <label for="link-type">{{ trans('main.type') }}</label>
            <select name="link-type" class="form-control" data-bind="value: app.models.link.type">
                <option value="embed">{{ trans('stream::main.embed') }}</option>
                <option value="video">{{ trans('stream::main.videojs') }}</option>
                <option value="external">{{ trans('stream::main.openExternal') }}</option>
            </select>
        </div>

        <!-- ko if: app.models.title.type == 'series' -->
        <div class="form-group">
            <label for="link-season">{{ trans('stream::main.seasonNum') }}</label>
            <select name="link-season" class="form-control" data-bind="value: app.models.link.season, foreach: app.models.title.seasons()">
                <option data-bind="attr: { value: $index()+1 }, text: $index()+1"></option> 
            </select>
        </div>
        <!-- /ko -->

        <!-- ko if: app.models.link.season -->
        <div class="form-group">
            <label for="link-episode">{{ trans('stream::main.episodeNum') }}</label>
            <select name="link-episode" class="form-control" data-bind="value: app.models.link.episode, foreach: app.models.title.seasons()[app.models.link.season()-1].episode">
                <option data-bind="attr: { value: $index()+1 }, text: $index()+1"></option> 
            </select>
        </div>
        <!-- /ko -->

        <button class="btn btn-primary" data-bind="click: addLink, enable: app.models.link.url">{{ trans('main.attach') }}</button>
   </div>
   <br>
   <hr>
   <br>

    @if(Helpers::hasAnyAccess(['links.delete','titles.edit', 'superuser']))
        <!-- ko if: app.models.title.links().length > 0 -->
        <table class="table table-striped table-centered table-bordered table-responsive">
            <thead>
                <tr>
                    <th>{{ trans('stream::main.label') }}</th>

                    <!-- ko if: app.models.title.type == 'series' -->
                    <th>{{ trans('stream::main.seasonNum') }}</th>
                    <th>{{ trans('stream::main.episodeNum') }}</th>
                    <!-- /ko -->

                    <th>{{ trans('main.type') }}</th>
                    <th>{{ trans('stream::main.reports') }}</th>
                    <th>{{ ucfirst(trans('dash.url')) }}</th>
                    <th>{{ trans('dash.actions') }}</th>
                    
                </tr>
            </thead>
            <tbody data-bind="foreach: app.models.title.links">
                <tr>
                    <td class="col-sm-2" data-bind="text: label"></td>

                    <!-- ko if: app.models.title.type == 'series' -->
                    <td class="col-sm-1" data-bind="text: season"></td>
                    <td class="col-sm-1" data-bind="text: episode"></td>
                    <!-- /ko -->

                    <td class="col-sm-2" data-bind="text: type"></td>
                    <td class="col-sm-1" data-bind="text: reports"></td>
                    <td class="col-sm-3" data-bind="text: url"></td>
                    @if(Helpers::hasAnyAccess(['links.delete','titles.edit', 'superuser']))
                    <td class="col-sm-2">
                        @if(Helpers::hasAnyAccess(['links.delete', 'superuser']))
                        <button type="button" class="btn-sm btn-danger btn" data-bind="click: $root.removeLink"><i class="fa fa-trash-o"></i> </button>
                        @endif
                        @if(Helpers::hasAnyAccess(['titles.edit', 'superuser']))
                        <button type="button" class="btn-sm btn-warning btn" data-bind="click: $root.editLink"><i class="fa fa-wrench"></i> </button>
                        @endif
                    </td>
                    @endif
                </tr>
            </tbody>
        </table>
        <!-- /ko -->
    @endif
</div>