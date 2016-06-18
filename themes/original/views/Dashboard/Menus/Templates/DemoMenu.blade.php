<!-- ko if: activeMenu && activeMenu() && activeMenu().position() === 'header' -->
<nav class="navbar navbar-inverse yamm" id="demo-menu" role="navigation">
	<div class="container">

	    <div class="navbar-header">
	    	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	        	<span class="sr-only">Toggle navigation</span>
	        	<span class="icon-bar"></span>
	        	<span class="icon-bar"></span>
	        	<span class="icon-bar"></span>
	      	</button>
	      	<a class="navbar-brand" href="#">
	      		@if ($options->getLogo())
	                <img src="{{ $options->getLogo() }}">
	            @else
	                <img src="{{ img('logo.png') }}">
	            @endif
	      	</a>
	    </div>

	    <div class="collapse navbar-collapse">
	    	<ul class="nav navbar-nav" data-bind="foreach: activeMenu().items().sort(function(a, b) { return a.weight() > b.weight() })">
	    		<!-- ko ifnot: children().length > 0 -->
	        	<li><a data-bind="text: label"></a></li>
	        	<!-- /ko -->

	        	<!-- ko if: children().length > 0 -->
	        	<li class="dropdown simple-dropdown">
	        	<a href="#" class="dropdown-toggle" data-hover="dropdown"><span data-bind="text: label"></span> <b class="caret"></b></a>
                    <ul class="dropdown-menu" data-bind="foreach: children">
                        <li><a href="#" data-bind="text: label"></a></li>
                    </ul>
		        </li>
	        	<!-- /ko -->
	      	</ul>
		</div>
  	</div>
</nav>
<!-- /ko -->