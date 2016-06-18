@extends('Main.Boilerplate')

@section('bodytag')
    <body id="dashboard">
    @stop

    @section('content')

        <section id="dash-container" class="with-filter-bar">

            @include('Dashboard.Partials.Sidebar')

            <div class="content">

                <div id="filter-row" class="row">
                    <button class="col-sm-1 btn btn-primary" data-bind="click: app.paginator.previousPage, enable: app.paginator.hasPrevious">
                        <fa class="fa fa-chevron-left"></fa> {{ trans('dash.previous') }}
                    </button>
                    <button class="col-sm-1 btn btn-primary" data-bind="click: app.paginator.nextPage, enable: app.paginator.hasNext">
                        {{ trans('dash.next') }} <fa class="fa fa-chevron-right"></fa>
                    </button>
                    <section class="col-sm-4 filter-dropdown">
                        <select class="form-control" data-bind="value: params.order">
                            <option value="">{{ trans('dash.sortBy') }}...</option>
                            <option value="created_atDesc">{{ trans('dash.createdAsc') }}</option>
                            <option value="created_atAsc">{{ trans('dash.createdDesc') }}</option>
                        </select>
                    </section>

                    <section class="col-sm-4">
                        <i class="fa fa-search"></i>
                        <input type="text" autocomplete="off" class="strip-input-styles" placeholder="{{ trans('main.search') }}..." data-bind="value: params.query, valueUpdate: 'keyup'">
                    </section>
                    <div class="col-sm-1"></div>
                    @if(Helpers::hasAccess('product_companies.create') || Helpers::hasSuperAccess())
                        <button class="col-sm-1 btn btn-primary" data-toggle="modal" data-target="#new-product-company-modal"><i class="fa fa-pencil"></i> {{ trans('dash.createNew') }}</button>
                    @endif
                </div>

                <section class="dash-padding">
                    <table class="table table-striped table-centered table-responsive">
                        <thead>
                        <tr>
                            <th>{{ trans('product_companies.logo') }}</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>{{ trans('main.member since') }}</th>

                            @if(Helpers::hasAnyAccess(['product_companies.edit', 'product_companies.delete']) || Helpers::hasSuperAccess())
                                <th>{{ trans('dash.actions') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody data-bind="foreach: sourceItems">
                        <tr>
                            <td class="col-sm-1"><img class="img-responsive col-sm-5" data-bind="attr: { src: logo ? logo.indexOf('//') > -1 ? logo : vars.urls.baseUrl+'/'+logo : vars.urls.baseUrl+'/assets/images/no_user_icon_big.jpg', alt: name }"></td>
                            <td class="col-sm-2"><a data-bind="text: name, attr: { href: vars.urls.baseUrl+'/'+vars.trans.tvnetworks+'/'+id }"></a></td>
                            <td class="col-sm-3" data-bind="text: description"></td>
                            <td class="col-sm-2" data-bind="text: created_at"></td>
                            @if(Helpers::hasAnyAccess(['product_companies.edit', 'product_companies.delete']) || Helpers::hasSuperAccess())
                                <td class="col-sm-1">
                                    @if(Helpers::hasAccess('product_companies.delete') || Helpers::hasSuperAccess())
                                        <button class="btn btn-danger btn-sm" data-bind="click: app.paginator.deleteItem"><i class="fa fa-trash-o"></i> </button>
                                    @endif
                                    @if(Helpers::hasAccess('product_companies.edit') || Helpers::hasSuperAccess())
                                        <a class="btn btn-primary btn-sm" data-bind="click: $root.populateModal.bind($data, id)"><i class="fa fa-wrench"></i> </a>
                                    @endif
                                </td>
                            @endif
                        </tr>
                        </tbody>
                    </table>
                </section>


                <div class="modal fade" id="new-product-company-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-pencil"></i> {{ trans('dash.createNew') }}</h4>
                            </div>
                            <div class="modal-body">
                                {{ Form::open(array('route' => 'prodCompanies.store', 'files' => true))}}
                                <div class="form-group">
                                    <label for="name">{{ trans('product_companies.name') }}</label>
                                    <input type="text" name="name" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="description">{{ trans('product_companies.description') }}</label>
                                    <input type="text" name="description" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="logo">{{ trans('product_companies.logo') }}</label>
                                    <input type="file" name="logo" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="cover_photo">{{ trans('product_companies.cover') }}</label>
                                    <input type="file" name="cover_photo" class="form-control">
                                </div>

                                <button type="submit" class="btn btn-success">{{ trans('main.submit') }}</button>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </section>

    @stop

    @section('ads')
    @stop

    @section('scripts')
        <script>
            vars.trans.prodCompanies = '<?php  echo trans("main.prodCompanies"); ?>';
            app.paginator.start(app.viewModels.product_companies, '.content', 15);
            app.viewModels.product_companies.registerEvents();
        </script>
@stop