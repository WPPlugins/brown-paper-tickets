<div id="event-feed" class="">
	<p><label for="feed-id"><strong>Feed ID</strong></label> <input id="feed-id" type="text" name="feed_id" value="<?= isset($data['feed_id']) ? $data['feed_id'] : '' ?>"></p>
    <p class="howto">The feed ID is the last set of digits at the end of the feed's url.</p>
    <div>
        <span id="feed-spinner" class="spinner"></span>
        <input name="refresh-feed" id="refresh-feed" class="button" value="Refresh Feed" type="submit">
    </div>
</div>
