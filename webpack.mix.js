const mix = require( 'laravel-mix' );
const fsExtra = require( "fs-extra" );
const path = require( "path" );
const cliColor = require( "cli-color" );
const emojic = require( "emojic" );
let WebpackRTLPlugin = require( 'webpack-rtl-plugin' );
const wpPot = require( 'wp-pot' );
require( "@tinypixelco/laravel-mix-wp-blocks" );
const min = Mix.inProduction() ? '.min' : '';
const isProduction = Mix.inProduction() ? true : false

if ( process.env.NODE_ENV === 'package' ) {

    mix.then( function () {

        let bundledir = path.basename( path.resolve( __dirname ) );
        let copyfrom = path.resolve( __dirname );
        let copyto = path.resolve( `${ bundledir }` );
        // Select All file then paste on list
        let includes = [
            'app',
            'assets',
            'languages',
            'resources',
            'templates',
            'vendor',
            'index.html',
            'README.txt',
            `${ bundledir }.php`
        ];
        fsExtra.ensureDir( copyto, function ( err ) {
            if ( err ) return console.error( err );
            includes.map( include => {
                fsExtra.copy( `${ copyfrom }/${ include }`, `${ copyto }/${ include }`, function ( err ) {
                    if ( err ) return console.error( err )
                    console.log( cliColor.white( `=> ${ emojic.smiley }  ${ include } copied...` ) );
                } )
            } );
            console.log( cliColor.white( `=> ${ emojic.whiteCheckMark }  Build directory created` ) );
        } );
    } );

    return;
} else {
    if ( Mix.inProduction() ) {
        let languages = path.resolve( 'languages' );
        fsExtra.ensureDir( languages, function ( err ) {
            if ( err ) return console.error( err ); // if file or folder does not exist
            wpPot( {
                package: 'The Post Grid',
                bugReport: '',
                src: '**/*.php',
                domain: 'the-post-grid',
                destFile: `languages/the-post-grid.pot`
            } );
        } );

    }
}

if ( process.env.NODE_ENV === 'development' || process.env.NODE_ENV === 'production' ) {
    mix.sass( 'src/scss/thepostgrid.scss', 'assets/css/thepostgrid.min.css' )
    mix.sass( 'src/scss/tpg-elementor.scss', 'assets/css/tpg-elementor.min.css' )
    mix.sass( 'src/scss/tpg-shortcode.scss', 'assets/css/tpg-shortcode.min.css' )
        .options( {
            terser: {
                extractComments: false
            },
            processCssUrls: false
        } )
        .webpackConfig( {
            plugins: [
                new WebpackRTLPlugin( {
                    filename: [ /(\.min.css)/i, '.rtl$1' ],
                    minify: isProduction,
                } )
            ],
        } )
}

