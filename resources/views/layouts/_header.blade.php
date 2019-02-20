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
		<!-- -->
		<div class="collapse navbar-collapse" id="app-navbar-collapse">
			<ul class="nav  navbar-nav mr-auto">
				@if(isset($categoryTree))
					<li class="nav-item dropdown">
						<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="categoryTree">
							所有类目
							<b class="caret"></b>
							<ul class="dropdown-menu" aria-labelledby="categoryTree">
								@each('layouts._category_item', $categoryTree, 'category')
							</ul>
						</a>
					</li>
				@endif
			</ul>
			<!-- 登录注册链接开始    collapse 崩溃  dropdown 下拉  toggle 切换  expanded 扩大  has popup 有 弹出-->

			<ul class="nav navbar-nav navbar-right">
				@guest
					<li><a href="{{ route('login') }}">登录</a></li>
	                <li><a href="{{ route('register') }}">注册</a></li>
                @else
                <li>
                    <a href="{{ route('cart.index') }}"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span></a>
                </li>
                <li class="dropdown">
                	<a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aira-expanded="false">
                		<span class="user-avatar pull-left" style="margin-right:8px; margin-top:-5px;">    <!--  responsive  响应 -->
                			<img src="https://iocaffcdn.phphub.org/uploads/images/201709/20/1/PtDKbASVcz.png?imageView2/1/w/60/h/60" class="img-responsive img-circle"  width="30px" height="30px" alt="avatar">
                		</span>
                		{{ Auth::user()->name }} <span class="caret"></span>
                	</a>
                	<ul class="dropdown-menu" role="menu">

                		<li>
    							<a href="{{ route('products.favorites') }}">我的收藏</a>
  						</li>
                		<li>
                			<a href="{{ route('user_addresses.index') }}">收货地址</a>
                        </li>
                        <li>
                            <a href="{{ route('orders.index') }}">我的订单</a>
                        </li>
						<li>
							<a href="{{ route('installments.index') }}">分期付款</a>
						</li>
                        <li>
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