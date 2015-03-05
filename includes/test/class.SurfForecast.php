<?php

class SurfForecast
{
  /**
   * get surf forecast
   *
   * local & dev
   * {"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"swellnetForecast","meta":{"id":3,"weatherzoneUrl":{"type":"varStore","meta":{"id":4,"operation":"fetch","var":"weatherzoneUrl"}},"weatherzoneLogin":{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"weatherzoneLogin"}},"weatherzonePass":{"type":"varStore","meta":{"id":6,"operation":"fetch","var":"weatherzonePass"}},"waveappUrl":{"type":"varStore","meta":{"id":7,"operation":"fetch","var":"waveappUrl"}},"waveappLogin":{"type":"varStore","meta":{"id":8,"operation":"fetch","var":"waveappLogin"}},"waveappPass":{"type":"varStore","meta":{"id":9,"operation":"fetch","var":"waveappPass"}},"drupalUrl":{"type":"concatenate","meta":{"id":10,"sources":[{"type":"varStore","meta":{"id":11,"operation":"fetch","var":"drupalUrl"}},"api\/anon\/surfreport\/"]}},"token":{"type":"varGet","meta":{"id":12,"var":"token"}},"location":{"type":"varUri","meta":{"id":13,"index":0}},"lat":{"type":"varGet","meta":{"id":14,"var":"lat"}},"lon":{"type":"varGet","meta":{"id":15,"var":"lon"}},"weatherStation":{"type":"varGet","meta":{"id":16,"var":"weatherStation"}},"tideStation":{"type":"varGet","meta":{"id":17,"var":"tideStation"}}}}
   */
  public function get()
  {
    return array(
       'validation' => array(
          'type' => 'validateToken',
          'meta' => array(
             'id' => 1,
             'token' => array(
                'type' => 'varGet',
                'meta' => array(
                   'id' => 2,
                   'var' => 'token',
                ),
             ),
          ),
       ),
       'type' => 'swellnetForecast',
       'meta' => array(
          'id' => 3,
          'weatherzoneUrl' => array(
             'type' => 'varStore',
             'meta' => array(
                'id' => 4,
                'operation' => 'fetch',
                'var' => 'weatherzoneUrl',
             ),
          ),
          'weatherzoneLogin' => array(
             'type' => 'varStore',
             'meta' => array(
                'id' => 5,
                'operation' => 'fetch',
                'var' => 'weatherzoneLogin',
             ),
          ),
          'weatherzonePass' => array(
             'type' => 'varStore',
             'meta' => array(
                'id' => 6,
                'operation' => 'fetch',
                'var' => 'weatherzonePass',
             ),
          ),
          'waveappUrl' => array(
             'type' => 'varStore',
             'meta' => array(
                'id' => 7,
                'operation' => 'fetch',
                'var' => 'waveappUrl',
             ),
          ),
          'waveappLogin' => array(
             'type' => 'varStore',
             'meta' => array(
                'id' => 8,
                'operation' => 'fetch',
                'var' => 'waveappLogin',
             ),
          ),
          'waveappPass' => array(
             'type' => 'varStore',
             'meta' => array(
                'id' => 9,
                'operation' => 'fetch',
                'var' => 'waveappPass',
             ),
          ),
          'drupalUrl' => array(
             'type' => 'concatenate',
             'meta' => array(
                'id' => 10,
                'sources' => array(
                   array(
                      'type' => 'varStore',
                      'meta' => array(
                         'id' => 11,
                         'operation' => 'fetch',
                         'var' => 'drupalUrl',
                      ),
                   ),
                   'api/anon/surfreport/',
                ),
             ),
          ),
          'token' => array(
             'type' => 'varGet',
             'meta' => array(
                'id' => 12,
                'var' => 'token',
             ),
          ),
          'location' => array(
             'type' => 'varUri',
             'meta' => array(
                'id' => 13,
                'index' => 0,
             ),
          ),
          'lat' => array(
             'type' => 'varGet',
             'meta' => array(
                'id' => 14,
                'var' => 'lat',
             ),
          ),
          'lon' => array(
             'type' => 'varGet',
             'meta' => array(
                'id' => 15,
                'var' => 'lon',
             ),
          ),
          'weatherStation' => array(
             'type' => 'varGet',
             'meta' => array(
                'id' => 16,
                'var' => 'weatherStation',
             ),
          ),
          'tideStation' => array(
             'type' => 'varGet',
             'meta' => array(
                'id' => 17,
                'var' => 'tideStation',
             ),
          ),
       ),
    );
  }
}