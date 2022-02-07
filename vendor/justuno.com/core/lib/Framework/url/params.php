<?php
/**
 * 2021-03-07 "Port the `df_nosid` function": https://github.com/justuno-com/core/issues/366
 * @used-by ju_url_frontend()
 * @return array(string => bool)
 */
function ju_nosid() {return ['_nosid' => true];}