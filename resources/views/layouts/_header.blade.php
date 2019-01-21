<nav class="navbar navbar-default navbar-static-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
				<span class="sr-only">Toggle Navigation 切换导航</span>
				<span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
			</button>
			<a href="{{ url('/') }}" class="navbar-brand">
				Shop
			</a>
		</div>
		<!-- collapse 崩溃 -->
		<div class="collapse navbar-collapse" id="app-navbar-collapse">
			<ul class="nav  navbar-nav">
				
			</ul>
			<!-- 登录注册链接开始  dropdown 下拉  toggle 切换  expanded 扩大-->

			<ul class="nav navbar-nav navbar-right">
				@guest
					<li><a href="{{ route('login') }}">登录</a></li>
	                <li><a href="{{ route('register') }}">注册</a></li>
                @else
                <li class="dropdown">
                	<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aira-expanded="false">
                		<span class="user-avatar pull-left" style="margin-right:8px; margin-top:-5px;">    <!--  responsive  响应 -->
                			<img src="https://iocaffcdn.phphub.org/uploads/images/201709/20/1/PtDKbASVcz.png?imageView2/1/w/60/h/60" class="img-responsive img-circle"  width="30px" height="30px" alt="avatar">
                		</span>
                		{{ Auth::user()->name }} <span class="caret"></span>
                	</a>
                	<ul class="dropdown-menu" role="menu">
                		<li>
                			<a href="{{ route('user_addresses.index') }}">收货地址</a>
                			<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                				退出登录
                			</a>
                			<form action="{{ route('logout') }}" id="logout-form" method="POST" style="display: none;">
                				{{ csrf_field() }}
                			</form>
                		</li>
                	</ul>
                </li>
                @endguest
			</ul>	
		</div>
	</div>
</nav>