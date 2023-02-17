<?php

use Illuminate\Support\Facades\Config;

it('should have a hash_id property', function() {
    Config::set('laravelhashid.slat', str()->password());
    
    $user = createFakeUser();
    
    expect($user)->toHaveKey('hash_id');
});