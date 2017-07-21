<div id="attendee-list" class="form-table">
	<img src="<?php echo esc_url( plugins_url( 'public/assets/img/bpt.png', $this->plugin_root() ) )?>">
	<h1>Attendee Lists</h1>
	<p>In order for this to work, the client ID you have set in the Account settings must in your authorized accounts.	</p>
	<div id="event-select"></div>
	<br>
	<div id="date-select"></div>
	<br>
	<div id="attendees"></div>
</div>
<!-- The event selector -->
<script id="event-select-template" type="text/html">
	{{#if events}}
		<h2>Select Event</h2>
		<select id="selected-event" name="selected" on-change="select-event" value="{{selected}}">
		{{#each events}}
			<option value="{{.}}">{{title}}</option>
		{{/each}}
		</select>
		<button id="refresh-events" class="button-secondary button-sm" on-click="refresh-events">Refresh Events</button>
	{{/if}}

	{{#if error}}
		<p>{{error}}</p>
	{{/if}}

	{{#if loading}}
	<div>
		<img class="loading" src="<?php echo esc_url( plugins_url( 'public/assets/img/loading.gif', $this->plugin_root() ) )?>">
		<p>Loading Events</p>
	</div>
	{{/if}}
</script>

<!-- The date selector -->

<script id="date-select-template" type="text/html">
{{#if dates.length}}
<h2>Select Dates</h2>
	{{#dates}}
		<label
			for="date-{{id}}"
			class="date-select"
		>
			{{formatDate(dateStart)}} - {{ formatTime(timeStart) }}
			<input
				id="date-{{id}}"
				type="checkbox"
				name="{{selected}}"
				value="{{id}}"
			>
		</label>
	{{/dates}}
	{{#if selected}}

	{{/if}}
{{/if}}
</script>

<!-- The attendee list -->

<script id="attendee-list-template" type="text/html">
{{#if dates.length}}
<h2>Attendees</h2>
{{#dates:i}}
	<h3>{{formatDate(dateStart)}} - {{ formatTime(timeStart) }}</h3>
	{{#attendees:i2}}
		{{#if inDate(this, dates[i]) }}
		<div class="attendee-row-{{i2 % 2 === 1 ? 'odd' : 'even'}}">
				<h4>{{firstName}} {{lastName}}</h4>
				<table>
					<!-- <thead>

							<th>Admission Level</th>
							<th>Ticket Number</th>
							<th>Section</th>
							<th>Row</th>
							<th>Seat</th>

					</thead> -->
					<tbody>
						<tr>
							<td>{{priceName(priceID)}}</td>
							<td>{{ticketNumber}}</td>
							<td>{{section || "N/A"}}</td>
							<td>{{row || "N/A"}}</td>
							<td>{{seat || "N/A"}}</td>
						</tr>
					</tbody>
				</table>
		</div>
		{{/if}}
	{{/attendees}}
	<hr>
{{/dates}}
{{/if}}

{{#unless loading}}
	{{#unless dates}}
		<h2>Attendees</h2>
		<p>Select dates to view attendees.</p>
	{{/unless}}
{{/unless}}

{{#if loading}}
<h2>Attendees</h2>
<div>
	<img class="loading" src="<?php echo esc_url( plugins_url( 'public/assets/img/loading.gif', $this->plugin_root() ) )?>">
	<p>Loading Attendees</p>
</div>
{{/if}}
</script>
