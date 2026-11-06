<form class="form-horizontal margin-top margin-bottom" method="POST" action="" autocomplete="off">
    {{ csrf_field() }}

    <h3 class="subheader">{{ __('kwtSMS account') }}</h3>
    <p class="help-block col-sm-offset-2">{{ __('Use the API username and password of your kwtSMS account, not your mobile number.') }}</p>

    <div class="form-group">
        <label for="kwtsms_username" class="col-sm-2 control-label">{{ __('API username') }}</label>
        <div class="col-sm-6">
            <input id="kwtsms_username" type="text" class="form-control input-sized-lg" name="settings[kwtsms.username]"
                   value="{{ old('settings.kwtsms.username', $settings['kwtsms.username']) }}">
        </div>
    </div>

    <div class="form-group">
        <label for="kwtsms_password" class="col-sm-2 control-label">{{ __('API password') }}</label>
        <div class="col-sm-6">
            <input id="kwtsms_password" type="password" class="form-control input-sized-lg" name="settings[kwtsms.password]"
                   value="{{ $settings['kwtsms.password'] }}" autocomplete="new-password">
        </div>
    </div>

    <div class="form-group">
        <label for="kwtsms_sender" class="col-sm-2 control-label">{{ __('Sender ID') }}</label>
        <div class="col-sm-6">
            <input id="kwtsms_sender" type="text" class="form-control input-sized" maxlength="11" name="settings[kwtsms.sender]"
                   value="{{ old('settings.kwtsms.sender', $settings['kwtsms.sender']) }}">
        </div>
    </div>

    <div class="form-group">
        <label for="kwtsms_test_mode" class="col-sm-2 control-label">{{ __('Test mode') }}</label>
        <div class="col-sm-6">
            <div class="controls">
                <input id="kwtsms_test_mode" type="checkbox" name="settings[kwtsms.test_mode]" value="on"
                       @if (old('settings.kwtsms.test_mode', $settings['kwtsms.test_mode']) == 'on') checked="checked" @endif>
            </div>
            <p class="form-help">{{ __('Messages are queued on kwtSMS but not delivered, and no credits are used.') }}</p>
        </div>
    </div>

    <h3 class="subheader">{{ __('Customer SMS') }}</h3>

    <div class="form-group">
        <label for="kwtsms_customer_new" class="col-sm-2 control-label">{{ __('New conversation') }}</label>
        <div class="col-sm-6">
            <div class="controls">
                <input id="kwtsms_customer_new" type="checkbox" name="settings[kwtsms.customer_new]" value="on"
                       @if (old('settings.kwtsms.customer_new', $settings['kwtsms.customer_new']) == 'on') checked="checked" @endif>
            </div>
            <p class="form-help">{{ __('Send an SMS to the customer when they start a conversation, if their profile has a phone number.') }}</p>
        </div>
    </div>

    <div class="form-group">
        <label for="kwtsms_customer_message" class="col-sm-2 control-label">{{ __('Message') }}</label>
        <div class="col-sm-6">
            <textarea id="kwtsms_customer_message" class="form-control" rows="3" name="settings[kwtsms.customer_message]">{{ old('settings.kwtsms.customer_message', $settings['kwtsms.customer_message']) }}</textarea>
            <p class="form-help">{{ __('Placeholders: {number}, {subject}, {name}.') }}</p>
        </div>
    </div>

    <h3 class="subheader">{{ __('Agent alerts') }}</h3>

    <div class="form-group">
        <label for="kwtsms_agent_new" class="col-sm-2 control-label">{{ __('New conversation') }}</label>
        <div class="col-sm-6">
            <div class="controls">
                <input id="kwtsms_agent_new" type="checkbox" name="settings[kwtsms.agent_new]" value="on"
                       @if (old('settings.kwtsms.agent_new', $settings['kwtsms.agent_new']) == 'on') checked="checked" @endif>
            </div>
            <p class="form-help">{{ __('Send an SMS to the numbers below when a customer starts a conversation.') }}</p>
        </div>
    </div>

    <div class="form-group">
        <label for="kwtsms_agent_numbers" class="col-sm-2 control-label">{{ __('Alert numbers') }}</label>
        <div class="col-sm-6">
            <input id="kwtsms_agent_numbers" type="text" class="form-control input-sized-lg" name="settings[kwtsms.agent_numbers]"
                   value="{{ old('settings.kwtsms.agent_numbers', $settings['kwtsms.agent_numbers']) }}" placeholder="965XXXXXXXX, 965XXXXXXXX">
            <p class="form-help">{{ __('International format, separated by commas.') }}</p>
        </div>
    </div>

    <div class="form-group">
        <label for="kwtsms_agent_message" class="col-sm-2 control-label">{{ __('Message') }}</label>
        <div class="col-sm-6">
            <textarea id="kwtsms_agent_message" class="form-control" rows="3" name="settings[kwtsms.agent_message]">{{ old('settings.kwtsms.agent_message', $settings['kwtsms.agent_message']) }}</textarea>
            <p class="form-help">{{ __('Placeholders: {number}, {subject}, {name}.') }}</p>
        </div>
    </div>

    <div class="form-group margin-top">
        <div class="col-sm-6 col-sm-offset-2">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </div>
</form>
