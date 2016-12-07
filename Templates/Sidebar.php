<nav class="navbar navbar-light bg-faded">
    <ul class="nav navbar-nav">
        <li class="nav-item <?php if ($this->controller == 'Index') echo 'active' ?>">
            <a class="nav-link" href="<?php echo APP_URL . '/index.php' ?>">首页</span></a>
        </li>
        <li class="nav-item <?php if ($this->controller == 'Status') echo 'active' ?>">
            <a class="nav-link" href="<?php echo APP_URL . '/index.php/status' ?>">Bot 状态</span></a>
        </li>
        <li class="nav-item <?php if ($this->controller == 'Plugins') echo 'active' ?>">
            <a class="nav-link" href="<?php echo APP_URL . '/index.php/plugins' ?>">插件</span></a>
        </li>
        <li class="nav-item <?php if ($this->controller == 'Settings') echo 'active' ?>">
            <a class="nav-link" href="<?php echo APP_URL . '/index.php/settings' ?>">设置</span></a>
        </li>
    </ul>
</nav>