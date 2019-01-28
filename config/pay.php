<?php

return [
    'alipay' => [
        'app_id'         => '2016092100559220',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhU0Tbz47zGoJVI+Gx0Rn/9xYL51RAQD94xZKQwXcDg3O3MPw1v6QZs7jBKp1lldN+OQPkmmIXNbBYjQ9XFdPw1pZfIBOIL/ZgRWQXZ2q6tFeqTAiSvTVJeM8Z86BXBMROWNPLGFAlWMa6Za9S2b+bXFVh24cneqckJ4MA/Izm3tL22UiDCj/dcXP1J23y3NYWL50PHLxDNmtGPAJ/mEJxyuAy+FMIK0Jpzxv5blyxOuP4qAoz53WIyqwd/70miX9lD9an0IIm8Ezy37UTFlM4ddNOZrh6qmOqXLtcHoNCdq1tiV0hGq/nzTiTzauPQt8q62kwZLN6kY0J0HxEqqZDQIDAQAB',
        'private_key'    => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCFTRNvPjvMaglUj4bHRGf/3FgvnVEBAP3jFkpDBdwODc7cw/DW/pBmzuMEqnWWV0345A+SaYhc1sFiND1cV0/DWll8gE4gv9mBFZBdnarq0V6pMCJK9NUl4zxnzoFcExE5Y08sYUCVYxrplr1LZv5tcVWHbhyd6pyQngwD8jObe0vbZSIMKP91xc/UnbfLc1hYvnQ8cvEM2a0Y8An+YQnHK4DL4UwgrQmnPG/luXLE64/ioCjPndYjKrB3/vSaJf2UP1qfQgibwTPLftRMWUzh1005muHqqY6pcu1weg0J2rW2JXSEar+fNOJPNq49C3yrraTBks3qRjQnQfESqpkNAgMBAAECggEARShMye0etxnYR1/DTASodC0ML3/Ns9ig370DwCv9E+mEBjM98zzVDBGP5C7PnLUkxdkEXzCTR58/a0SxBQRjZHWucZJbdlAydu8KHBedwf/nvZ00XXESWHrBLxYLNQrLZ9unCt5V1Gs7Xi8PfFwt6fffqMiu3hsHKVxl3XI95g7dBWzohm6lwDFRZQ1MZy5vjy7BufQI8EbzPTWqYf3qOSBRS7vSSqvA4GU6yFKri/KLvdoEhcy/e9nc5SMvYaiIn60r4zA9/mXYINMCvHfspQyPdaecrFtHbmR95wI7aKvPjfa9+OXvy6ncRsza6HvSdhzQwLFgKu2YcCE2G2mQwQKBgQC68fptokuaIiI9+2VE2agQGjDZ6loLDe7wT1ur4xBEyR4DQjBEz/DkiLBuf/4qyjFUO9vXP4C+6iYXyAhtr+Eix4bYJ58a3CGsdtBVb7ZT/Q9I9UPm0ZxWb76AINGbxbhr/hbTCGbhs+g66UGPs50S2iTu0gedBQuY1DuiVOTo0QKBgQC2il5szQPzj9WU6uqZ7VKsjwH3lkLE4UpYxLlU2xyiwcE/IrLnNQrQOgFLOXbL8Fn/S2EeY27yKNe3gwkkjhEv8C1fnKvqzL9tBM2rcjTfPe7TaI38lNMJIBGH3tEmTmbv+vG6CjJv7Z76gCDCuxp4h/EPCjQf3qCrcFiP7KD7fQKBgQCKq1WsnzdfNLSbr2/1+1HL5/GWo1x5WLOFdAg23y5BJ8Hoft9ZG7m/e5kLzktVqDehGOnAp60zcHIjL9D4s/7XEpP2oKVHgkRELrnotf3UHSGKZ8wfWhqSjz8Kwc1Zs/dRu99oWJIgF5hKop1sH7qCsme08vyMv6JTkhJOHpTSEQKBgGMukZm6V3BKcblFXw0d1vLtjRsSqNrCspvC4BRMLaX9ctu0JdLcjjCPo85UvciXor7FHVLeohSvvm32o8wZ6RrL/EtcHEkq0MQSfRGvryyxQQfbYnKgBn6JzxytI6UVqnawOhFkov0P9naTkblc4kglTQRn/eRnGZOtKmGhJ+MdAoGACrFb3yalxa0x1dJ5O6kP/mqTH+XYy0VV93ovS/McqUDX9z1lXukmVkmeXHdIny+3BDrsVA8W8OJudP7M4dvaGxq8lRGLH1jGgjxwVJbsddq6fyy1AiiUVpOAoYBRPnBBymATwN7Bs2kP/IVM8zy+lSZiEgQkQvI8mTyz52BX8dc=',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
