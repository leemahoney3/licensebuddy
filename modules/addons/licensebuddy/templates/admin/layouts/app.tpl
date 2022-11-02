<link rel="stylesheet" href="{$systemURL}/modules/addons/licensebuddy/assets/css/app.min.css">

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="{$moduleLink}&page={PageHelper::getPageInfo('dashboard', 'slug')}" class="navbar-brand"><i class="fal fa-lock-alt"></i>&nbsp; License Buddy</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse-1">
            
            <ul class="nav navbar-nav">

                {foreach $allNavPages as $page}

                <li {if $currentPage eq $page.slug || $page.dropdown || $currentPage eq $page.extraNavActive}class="{if $currentPage eq $page.slug || $currentPage eq $page.extraNavActive}active{/if} {if $page.dropdown}dropdown{/if}"{/if}>
                    
                    <a href="{$moduleLink}&page={$page.slug}" {if $page.dropdown}data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"{/if}>
                        {$page.icon} {$page.name}
                        {if $page.dropdown}<span class="caret"></span>{/if}
                    </a>

                    {if $page.dropdown}
                        <ul class="dropdown-menu">
                            {foreach $page.links as $link => $slug}
                                <li><a href="{$moduleLink}&page={$page.slug}{$slug}">{$link}</a></li>
                            {/foreach}
                        </ul>
                    {/if}
                    
                </li>

                {/foreach}

            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li><a href="https://leemahoney.tech/" class="lmtech-logo">LMTech</a></li>
            </ul>

        </div>
    </div>
</nav>

{block name="content"}{/block}
{block name="scripts"}{/block}

<script type="text/javascript">
    $(document).ready(function () {
        $('#contentarea').find('h1').first().remove();
    });
</script>