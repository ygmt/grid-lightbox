<?php
/**
 * Created by PhpStorm.
 * User: wangh
 * Date: 2020/9/25
 * Time: 14:16
 */

namespace Encore\Grid\Lightbox;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PhotoDisplayer extends AbstractDisplayer
{
	public $options = [
		'type' => 'image'
	];

	protected function script()
	{
		$options = json_encode($this->options);

		return <<<SCRIPT
		 $('[data-magnify=gallery]').magnify(
        {  draggable: false,modalWidth: 640,modalHeight: 640,
        footToolbar: [
           'zoomIn',
           'zoomOut',
           'fullscreen',
           'actualSize',
           'rotateRight'
          ]
        });
SCRIPT;
	}

	public function zooming()
	{
		$this->options = array_merge($this->options, [
			'mainClass' => 'mfp-with-zoom',
			'zoom' => [
				'enabled' => true,
				'duration' => 300,
				'easing' => 'ease-in-out',
			]
		]);
	}

	public function display(array $options = [])
	{
		if (empty($this->value)) {
			return '';
		}

		if ($this->value instanceof Arrayable) {
			$this->value = $this->value->toArray();
		}

		$server = Arr::get($options, 'server');
		$width = Arr::get($options, 'width', 200);
		$height = Arr::get($options, 'height', 200);
		$class = Arr::get($options, 'class', 'thumbnail');
		$class = collect((array)$class)->map(function ($item) {
			return 'img-'. $item;
		})->implode(' ');

		if (Arr::get($options, 'zooming')) {
			$this->zooming();
		}

		Admin::script($this->script());

		return collect((array)$this->value)->filter()->map(function ($path) use ($server, $width, $height, $class) {
			if (url()->isValidUrl($path) || strpos($path, 'data:image') === 0) {
				$src = $path;
			} elseif ($server) {
				$src = rtrim($server, '/') . '/' . ltrim($path, '/');
			} else {
				$src = Storage::disk(config('admin.upload.disk'))->url($path);
			}

			return <<<HTML
<a href="$src" class="grid-popup-link">
    <img width='{$width}' height='{$height}' src='$src' data-src='$src' data-magnify='gallery'>
</a>
HTML;
		})->implode('&nbsp;');
	}
}