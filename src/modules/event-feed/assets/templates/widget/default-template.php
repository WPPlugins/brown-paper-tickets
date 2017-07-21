{{ #events }}
    <div class="bpt-event-feed-event row">
        <div class="col col-3 bpt-event-feed-image-container">
			<a href="https://www.brownpapertickets.com/event/{{id}}" target="_blank">
		{{ #images.0.large }}
            <img class="bpt-event-feed-event-image" src="{{ images.0.large }}">
		{{ /images.0.large }}
		{{ ^images.0.large }}
			<img
				class="bpt-event-feed-event-image bpt-event-feed-event-default-image"
				src="{{ defaultImage }}"
			>
		{{ /images.0.large}}
			</a>
        </div>
        <div class="col col-9 bpt-event-feed-event-details-container">
			<h4 class="bpt-event-feed-event-title no-margin"><a href="https://www.brownpapertickets.com/event/{{id}}" target="_blank">{{ title }}</a></h4>
            <span class="bpt-event-feed-event-date font-bitter">{{ dates.0.dateStart }}</span>
            <p class="bpt-event-feed-event-description">
				{{{ shortDescription }}}
			</p>
        </div>
    </div>
{{ /events }}
