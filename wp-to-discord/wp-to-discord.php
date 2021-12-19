<?php
/*
  Plugin Name: WP to Discord
  Plugin URI: https://yoshidayasutaka.github.io/
  Description: 記事投稿・更新時にDiscordにメッセージ送信するプラグインです
  Version: 1.0.0
  Author: Yasutaka Yoshida
  Author URI: https://yoshidayasutaka.github.io/
 */
 
function post_discord($content,$embeds=null){
	$jsonData=$embeds!==null?json_encode(array('content'=>$content,'embeds'=>$embeds)):json_encode(array('content'=>$content));
	$ch=curl_init('WebHook URL');
	curl_setopt($ch,CURLOPT_POST,true);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($ch,CURLOPT_POSTFIELDS,$jsonData);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	$res=curl_exec($ch);
	curl_close($ch);
	return $res;
}

function post_notify($new,$old,$post){
	if($new!=='publish')return;
	switch($old){
		case 'new':
		case 'draft':
		case 'pending':
		case 'auto-draft':
		case 'future':
			post_discord('新規投稿',array(array('title'=>get_the_title($post),'url'=>get_permalink($post),'description'=>get_the_excerpt($post),'color'=>30719)));
	}
}

function update_notify($new,$old,$post){
	if($post->post_status!=='publish')return;
	post_discord('記事更新',array(array('title'=>$post->post_title,'url'=>get_permalink($post->ID),'description'=>get_the_excerpt($post->ID),'color'=>30719)));
}

add_action('transition_post_status','post_notify',10,3);
add_action('post_updated','update_notify',10,3);

?>