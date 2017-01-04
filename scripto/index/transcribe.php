<?php
$titleArray = array(__('Scripto'), __('Transcribe Page'));
queue_css_file('scripto-transcribe');
$head = array('title' => html_escape(implode(' | ', $titleArray)), 'bodyid' => 'transcribePage');
echo head($head);
if (get_option('scripto_image_viewer') == 'openlayers') {
    echo js_tag('OpenLayers');
    // jQuery is enabled by default in Omeka and in most themes.
    // echo js_tag('jquery', 'javascripts/vendor');
}
?>
<script type="text/javascript">
jQuery(document).ready(function() {

    // Handle edit transcription page.
    jQuery('#scripto-transcription-page-edit').click(function() {
        jQuery('#scripto-transcription-page-edit').
            prop('disabled', true).
            text('<?php echo __('Saving transcription...'); ?>');
        jQuery.post(
            <?php echo js_escape(url('scripto/index/page-action')); ?>,
            {
                page_action: 'edit',
                page: 'transcription',
                item_id: <?php echo js_escape($this->doc->getId()); ?>,
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>,
                wikitext: jQuery('#scripto-transcription-page-wikitext').val()
            },
            function(data) {
                jQuery('#scripto-transcription-page-edit').
                    prop('disabled', false).
                    text('<?php echo __('Save transcription'); ?>');
                jQuery('#scripto-transcription-page-html').html(data);
            }
        );
    });

    // Handle edit talk page.
    jQuery('#scripto-talk-page-edit').click(function() {
        jQuery('#scripto-talk-page-edit').
            prop('disabled', true).
            text('<?php echo __('Saving discussion...'); ?>');
        jQuery.post(
            <?php echo js_escape(url('scripto/index/page-action')); ?>,
            {
                page_action: 'edit',
                page: 'talk',
                item_id: <?php echo js_escape($this->doc->getId()); ?>,
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>,
                wikitext: jQuery('#scripto-talk-page-wikitext').val()
            },
            function(data) {
                jQuery('#scripto-talk-page-edit').
                    prop('disabled', false).
                    text('<?php echo __('Save discussion'); ?>');
                jQuery('#scripto-talk-page-html').html(data);
            }
        );
    });

    // Handle default transcription/talk visibility.
    if (window.location.hash == '#discussion') {
        jQuery('#scripto-transcription').hide();
        jQuery('#scripto-page-show').text('<?php echo __('show transcription'); ?>');
    } else {
        window.location.hash = '#transcription'
        jQuery('#scripto-talk').hide();
        jQuery('#scripto-page-show').text('<?php echo __('show discussion'); ?>');
    }

    // Handle transcription/talk visibility.
    jQuery('#scripto-page-show').click(function(event) {
        event.preventDefault();
        if ('#transcription' == window.location.hash) {
            window.location.hash = '#discussion';
            jQuery('#scripto-transcription').hide();
            jQuery('#scripto-talk').show();
            jQuery('#scripto-page-show').text('<?php echo __('show transcription'); ?>');
        } else {
            window.location.hash = '#transcription';
            jQuery('#scripto-talk').hide();
            jQuery('#scripto-transcription').show();
            jQuery('#scripto-page-show').text('<?php echo __('show discussion'); ?>');
        }
    });

    // Toggle show transcription edit.
    jQuery('#scripto-transcription-edit-show').click(function(event) {
        event.preventDefault();
        var clicks = jQuery(this).data('clicks');
        if (!clicks) {
            jQuery(this).text('<?php echo __('hide edit'); ?>');
            jQuery('#scripto-transcription-edit').slideDown('fast');
        } else {
            jQuery(this).text('<?php echo __('edit'); ?>');
            jQuery('#scripto-transcription-edit').slideUp('fast');
        }
        jQuery(this).data("clicks", !clicks);
    });

    // Toggle show talk edit.
    jQuery('#scripto-talk-edit-show').click(function(event) {
        event.preventDefault();
        var clicks = jQuery(this).data('clicks');
        if (!clicks) {
            jQuery(this).text('<?php echo __('hide edit'); ?>');
            jQuery('#scripto-talk-edit').slideDown('fast');
        } else {
            jQuery(this).text('<?php echo __('edit'); ?>');
            jQuery('#scripto-talk-edit').slideUp('fast');
        }
        jQuery(this).data("clicks", !clicks);
    });

    <?php if ($this->scripto->isLoggedIn()): ?>

    // Handle default un/watch page.
    <?php if ($this->doc->isWatchedPage()): ?>
    jQuery('#scripto-page-watch').
        data('watch', true).
        text('<?php echo __('Unwatch page'); ?>').
        css('float', 'none');
    <?php else: ?>
    jQuery('#scripto-page-watch').
        data('watch', false).
        text('<?php echo __('Watch page'); ?>').
        css('float', 'none');
    <?php endif; ?>

    // Handle un/watch page.
    jQuery('#scripto-page-watch').click(function() {
        if (!jQuery(this).data('watch')) {
            jQuery(this).prop('disabled', true).text('<?php echo __('Watching page...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>,
                {
                    page_action: 'watch',
                    page: 'transcription',
                    item_id: <?php echo js_escape($this->doc->getId()); ?>,
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                },
                function(data) {
                    jQuery('#scripto-page-watch').
                        data('watch', true).
                        prop('disabled', false).
                        text('<?php echo __('Unwatch page'); ?>');
                }
            );
        } else {
            jQuery(this).prop('disabled', true).text('<?php echo __('Unwatching page...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>,
                {
                    page_action: 'unwatch',
                    page: 'transcription',
                    item_id: <?php echo js_escape($this->doc->getId()); ?>,
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                },
                function(data) {
                    jQuery('#scripto-page-watch').
                        data('watch', false).
                        prop('disabled', false).
                        text('<?php echo __('Watch page'); ?>');
                }
            );
        }
    });

    <?php endif; // end isLoggedIn() ?>

    <?php if ($this->scripto->canProtect()): ?>

    // Handle default un/protect transcription page.
    <?php if ($this->doc->isProtectedTranscriptionPage()): ?>
    jQuery('#scripto-transcription-page-protect').
        data('protect', true).
        text('<?php echo __('Unprotect page'); ?>').
        css('float', 'none');
    <?php else: ?>
    jQuery('#scripto-transcription-page-protect').
        data('protect', false).
        text('<?php echo __('Protect page'); ?>').
        css('float', 'none');
    <?php endif; ?>

    // Handle un/protect transcription page.
    jQuery('#scripto-transcription-page-protect').click(function() {
        if (!jQuery(this).data('protect')) {
            jQuery(this).prop('disabled', true).text('<?php echo __('Protecting...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>,
                {
                    page_action: 'protect',
                    page: 'transcription',
                    item_id: <?php echo js_escape($this->doc->getId()); ?>,
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>,
                    wikitext: jQuery('#scripto-transcription-page-wikitext').val()
                },
                function(data) {
                    jQuery('#scripto-transcription-page-protect').
                        data('protect', true).
                        prop('disabled', false).
                        text('<?php echo __('Unprotect page'); ?>');
                }
            );
        } else {
            jQuery(this).prop('disabled', true).text('<?php echo __('Unprotecting page...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>,
                {
                    page_action: 'unprotect',
                    page: 'transcription',
                    item_id: <?php echo js_escape($this->doc->getId()); ?>,
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                },
                function(data) {
                    jQuery('#scripto-transcription-page-protect').
                        data('protect', false).
                        prop('disabled', false).
                        text('<?php echo __('Protect page'); ?>');
                }
            );
        }
    });

    // Handle default un/protect talk page.
    <?php if ($this->doc->isProtectedTalkPage()): ?>
    jQuery('#scripto-talk-page-protect').
        data('protect', true).
        text('<?php echo __('Unprotect page'); ?>').
        css('float', 'none');
    <?php else: ?>
    jQuery('#scripto-talk-page-protect').
        data('protect', false).
        text('<?php echo __('Protect page'); ?>').
        css('float', 'none');
    <?php endif; ?>

    // Handle un/protect talk page.
    jQuery('#scripto-talk-page-protect').click(function() {
        if (!jQuery(this).data('protect')) {
            jQuery(this).
                prop('disabled', true).
                text('<?php echo __('Protecting page...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>,
                {
                    page_action: 'protect',
                    page: 'talk',
                    item_id: <?php echo js_escape($this->doc->getId()); ?>,
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                },
                function(data) {
                    jQuery('#scripto-talk-page-protect').
                        data('protect', true).
                        prop('disabled', false).
                        text('<?php echo __('Unprotect page'); ?>');
                }
            );
        } else {
            jQuery(this).prop('disabled', true).text('<?php echo __('Unprotecting page...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>,
                {
                    page_action: 'unprotect',
                    page: 'talk',
                    item_id: <?php echo js_escape($this->doc->getId()); ?>,
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                },
                function(data) {
                    jQuery('#scripto-talk-page-protect').
                        data('protect', false).
                        prop('disabled', false).
                        text('<?php echo __('Protect page'); ?>');
                }
            );
        }
    });

    <?php endif; // end canProtect() ?>
    <?php if ($this->scripto->canExport()): ?>

    jQuery('#scripto-transcription-page-import').click(function() {
        jQuery(this).prop('disabled', true).text('<?php echo __('Importing page...'); ?>');
        jQuery.post(
            <?php echo js_escape(url('scripto/index/page-action')); ?>,
            {
                page_action: 'import-page',
                page: 'transcription',
                item_id: <?php echo js_escape($this->doc->getId()); ?>,
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>
            },
            function(data) {
                jQuery('#scripto-transcription-page-import').
                    prop('disabled', false).
                    text('<?php echo __('Import page'); ?>');
            }
        );
    });

    jQuery('#scripto-transcription-document-import').click(function() {
        jQuery(this).prop('disabled', true).text('<?php echo __('Importing document...'); ?>');
        jQuery.post(
            <?php echo js_escape(url('scripto/index/page-action')); ?>,
            {
                page_action: 'import-document',
                page: 'transcription',
                item_id: <?php echo js_escape($this->doc->getId()); ?>,
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>
            },
            function(data) {
                jQuery('#scripto-transcription-document-import').
                    prop('disabled', false).
                    text('<?php echo __('Import document'); ?>');
            }
        );
    });

    <?php endif; // end canExport() ?>
});
</script>

<?php
    $page_id = $this->doc->getId();
    set_current_record('item',get_record_by_id('item', $page_id));
    $collection = get_collection_for_item();
    $collection_link = link_to_collection_for_item();
?>

<?php $base_Dir = basename(getcwd()); ?>

<?php if (!is_admin_theme()): ?>
<h1><?php echo $head['title']; ?></h1>
<?php endif; ?>
<div id="primary">
<?php echo flash(); ?>

    <ul class="breadcrumb">
        <li><a href="<?php echo WEB_ROOT; ?>">Home</a><span class="divider">/</span></li>
        <li><?php echo link_to_collection_for_item(); ?><span class="divider">/</span></li>
        <li><li><a href="<?php echo url(array('controller' => 'items', 'action' => 'show', 'id' => $this->doc->getId()), 'id'); ?>"><?php echo $this->doc->getTitle(); ?></a></li>
    </ul>
    <div id="scripto-transcribe" class="scripto">
        <!-- navigation -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#"><?php echo __('Differences'); ?></a></li>
        <?php if ($this->scripto->isLoggedIn()): ?>
            <li><span><?php echo __('Logged in as %s', '<a href="' . html_escape(url('scripto')) . '">' . $this->scripto->getUserName() . '</a>'); ?></span></li>
            <li><span>(<a href="<?php echo html_escape(url('scripto/index/logout')); ?>"><?php echo __('logout'); ?></a>)</span></li>
            <li><a href="<?php echo html_escape(url('scripto/watchlist')); ?>"><?php echo __('Your watchlist'); ?></a> </li>
        <?php else: ?>
            <li><a href="<?php echo html_escape(url('scripto/index/login')); ?>"><?php echo __('Log in to Scripto'); ?></a></li>
        <?php endif; ?>
            <li><a href="<?php echo html_escape(url('scripto/recent-changes')); ?>"><?php echo __('Recent changes'); ?></a></li>
            <li><a href="<?php echo html_escape(url(array('controller' => 'items', 'action' => 'show', 'id' => $this->doc->getId()), 'id')); ?>"><?php echo __('View item'); ?></a></li>
            <li><a href="<?php echo html_escape(url(array('controller' => 'files', 'action' => 'show', 'id' => $this->doc->getPageId()), 'id')); ?>"><?php echo __('View file'); ?></a></li>
        </ul>

        <h2><?php if ($this->doc->getTitle()): ?><?php echo $this->doc->getTitle(); ?><?php else: ?><?php echo __('Untitled Document'); ?><?php endif; ?></h2>
        <?php if ($this->scripto->canExport()): ?><div><?php echo $this->formButton('scripto-transcription-document-import', __('Import document'), array('style' => 'display:inline; float:none;')); ?></div><?php endif; ?>

        <div>
            <div><strong><?php echo metadata($this->file, array('Dublin Core', 'Title')); ?></strong></div>
            <div>image <?php echo html_escape($this->paginationUrls['current_page_number']); ?> of <?php echo html_escape($this->paginationUrls['number_of_pages']); ?></div>
            <div>
                <?php if ($this->doc->getIsReferencedBy()):echo "more information: <a href=\"" . $this->doc->getIsReferencedBy() . "\" target=\"_blank\">digital collection</a>"; else: endif; ?>
                <?php //echo metadata($this->$file, array('Dublin Core', 'Source')); ?>
                <?php //echo metadata($item, array('Dublin Core', 'Relation')); ?>
            </div>
        </div>

        <!-- document viewer -->
        <?php echo file_markup($this->file, array('imageSize' => 'fullsize')); ?>

        <div id="transcription-block" style="display: inline-block;">
			<!-- transcription -->
			<div id="scripto-transcription">
			<?php if ($this->doc->canEditTranscriptionPage()): ?>
				<div id="scripto-transcription-edit" style="display: none;">
				<?php if ($this->doc->isProtectedTranscriptionPage()): ?>
					<div class="alert alert-error">
						<strong>This transcription is complete!</strong>
					</div><!--alert alert-error-->
					<div id="scripto-transcription-page-html">
						<?php echo $this->transcriptionPageHtml; ?>
					</div>
					<?php else: ?>
					<div class="alert alert-info">
						<strong>This item is editable!</strong>
					</div><!--alert alert-info-->
					<div><?php echo $this->formTextarea('scripto-transcription-page-wikitext', $this->doc->getTranscriptionPageWikitext(), array('cols' => '76', 'rows' => '16')); ?></div>
					<?php endif; ?>
					<div>
						<?php echo $this->formButton('scripto-transcription-page-edit', __('Save transcription'), array('class' => 'btn btn-primary')); ?>
					</div>
				</div><!-- #scripto-transcription-edit -->
			<?php else: ?>
				<p><?php echo __('You don\'t have permission to transcribe this page.'); ?></p>
			<?php endif; ?>

				<h2><?php echo __('Current Page Transcription'); ?>
				<?php if ($this->doc->canEditTranscriptionPage()): ?> [<a href="#" id="scripto-transcription-edit-show"><?php echo __('edit'); ?></a>]<?php endif; ?>
				<?php if ($this->scripto->canProtect()): ?> [<a href="<?php echo html_escape($this->doc->getTranscriptionPageMediawikiUrl()); ?>"><?php echo __('wiki'); ?></a>]<?php endif; ?>
				[<a href="<?php echo html_escape(url(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => 0), 'scripto_history')); ?>"><?php echo __('history'); ?></a>]</h2>
				<div>
					<?php if ($this->scripto->isLoggedIn()): ?><?php echo $this->formButton('scripto-page-watch'); ?> <?php endif; ?>
					<?php if ($this->scripto->canProtect()): ?><?php echo $this->formButton('scripto-transcription-page-protect'); ?> <?php endif; ?>
					<?php if ($this->scripto->canExport()): ?><?php echo $this->formButton('scripto-transcription-page-import', __('Import page'), array('style' => 'display:inline; float:none;')); ?><?php endif; ?>
				</div>
				<div id="scripto-transcription-page-html"><?php echo $this->transcriptionPageHtml; ?></div>
			</div><!-- #scripto-transcription -->

			<!-- discussion -->
			<div id="scripto-talk">
				<?php if ($this->doc->canEditTalkPage()): ?>
				<div id="scripto-talk-edit" style="display: none;">
					<div><?php echo $this->formTextarea('scripto-talk-page-wikitext', $this->doc->getTalkPageWikitext(), array('cols' => '76', 'rows' => '16')); ?></div>
					<div>
						<?php echo $this->formButton('scripto-talk-page-edit', __('Save discussion'), array('style' => 'display:inline; float:none;')); ?>
					</div>
				</div><!-- #scripto-talk-edit -->
				<?php else: ?>
				<p><?php echo __('You don\'t have permission to discuss this page.'); ?></p>
				<?php endif; ?>
				<h2><?php echo __('Current Page Discussion'); ?>
				<?php if ($this->doc->canEditTalkPage()): ?> [<a href="#" id="scripto-talk-edit-show"><?php echo __('edit'); ?></a>]<?php endif; ?>
				<?php if ($this->scripto->canProtect()): ?> [<a href="<?php echo html_escape($this->doc->getTalkPageMediawikiUrl()); ?>"><?php echo __('wiki'); ?></a>]<?php endif; ?>
				[<a href="<?php echo html_escape(url(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => 1), 'scripto_history')); ?>"><?php echo __('history'); ?></a>]</h2>
				<div>
					<?php if ($this->scripto->canProtect()): ?><?php echo $this->formButton('scripto-talk-page-protect'); ?> <?php endif; ?>
				</div>
				<div id="scripto-talk-page-html"><?php echo $this->talkPageHtml; ?></div>
			</div><!-- #scripto-talk -->
		</div>

        <!-- pagination -->
        <div class="pagination">
        <?php if (isset($this->paginationUrls['previous'])): ?><a><button type="submit" class="btn btn-mini nav-btn" onClick="parent.location='<?php echo html_escape($this->paginationUrls['previous']); ?>'">prev</button></a><?php endif; ?>
		<?php if (isset($this->paginationUrls['next'])): ?><a><button type="submit" class="btn btn-mini nav-btn" onClick="parent.location='<?php echo html_escape($this->paginationUrls['next']); ?>'">next</button></a><?php endif; ?>
        <?php if (intval(html_escape($this->paginationUrls['number_of_pages'])) > 1): ?><a><button type="submit" class="btn btn-mini nav-btn" onClick="parent.location='<?php echo html_escape(url(array('controller' => 'items', 'action' => 'show', 'id' => $this->doc->getId()), 'id')); ?>'">all pages</button></a><?php endif; ?>
        </div>
        <a href="#" id="scripto-page-show"></a>





    </div><!-- #scripto-transcribe -->
</div>
<?php echo foot(); ?>
