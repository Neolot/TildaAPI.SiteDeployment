<?php
function tilda_createProjectFiles($project_data) {
    if ( $project_data ) {
        if ( ! file_exists( DOCUMENT_ROOT . 'css' ) ) {
            mkdir( DOCUMENT_ROOT . 'css', 0755, true );
            tilda_getFiles($project_data['css'], 'css');
        }

        if ( ! file_exists( DOCUMENT_ROOT . 'js' ) ) {
            mkdir( DOCUMENT_ROOT . 'js', 0755, true );
            tilda_getFiles($project_data['js'], 'js');
        }

        if ( ! file_exists( DOCUMENT_ROOT . 'img' ) ) {
            mkdir( DOCUMENT_ROOT . 'img', 0755, true );
            tilda_getFiles($project_data['images'], 'img');
        }

        file_put_contents( DOCUMENT_ROOT . '.htaccess', $project_data['htaccess'] );
    }
}

function tilda_getFiles($data, $type) {
    if ( $data && $type ) {
        foreach ( $data as $file ) {
            if ( ! file_exists( DOCUMENT_ROOT . $type . '/' . $file['to'] ) ) {
                $ch = curl_init($file['from']);
                $handle = fopen( DOCUMENT_ROOT . $type . '/' . $file['to'], 'wb' );
                curl_setopt($ch, CURLOPT_FILE, $handle);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_exec($ch);
                curl_close($ch);
                fclose($handle);
            }
        }
    }
}

function tilda_createPages($pages_data) {
    if ( $pages_data ) {
        $url_data['publickey'] = PUBLICKEY;
        $url_data['secretkey'] = SECRETKEY;

        foreach ( $pages_data as $page ) {
            if ( $page['published'] ) {
                $pagepath = DOCUMENT_ROOT . 'page' . $page['id'] . '.html';
                if ( ! file_exists( $pagepath ) ) {
                    $url_data['pageid'] = $page['id'];
                    $url_params = http_build_query($url_data);

                    $method = 'getpagefullexport';
                    $url = APIURL . $method . '/?' . $url_params;
                    $query_page = file_get_contents($url);
                    $response_page = json_decode($query_page, true);

                    if ( $response_page['status'] == 'FOUND' ) {
                        tilda_getFiles($response_page['result']['css'], 'css');
                        tilda_getFiles($response_page['result']['js'], 'js');
                        tilda_getFiles($response_page['result']['images'], 'img');
                        file_put_contents( $pagepath, $response_page['result']['html'] );
                    }
                }
            }
        }
    }
}
