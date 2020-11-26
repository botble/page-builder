<?php

namespace Botble\PageBuilder\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Page\Repositories\Interfaces\PageInterface;
use Botble\PageBuilder\Http\Requests\PageBuilderRequest;
use Botble\Setting\Supports\SettingStore;
use Botble\Theme\Asset;
use Theme;

class PageBuilderController extends BaseController
{
    /**
     * @var PageInterface
     */
    protected $pageRepository;

    /**
     * PageBuilderController constructor.
     * @param PageInterface $pageRepository
     */
    public function __construct(PageInterface $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * @param int $id
     * @param Asset $asset
     * @param SettingStore $settingStore
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getDesign($id, Asset $asset, SettingStore $settingStore)
    {
        $page = $this->pageRepository->findOrFail($id);

        $theme = Theme::uses(Theme::getThemeName())->layout($settingStore->get('layout', 'default'));

        // Fire event global assets.
        $theme->fire('asset', $asset);
        // Fire event before render theme.
        $theme->fire('beforeRenderTheme', $theme);
        // Fire event before render layout.
        $theme->fire('beforeRenderLayout.' . $settingStore->get('layout', 'default'), $theme);

        $theme->setUpContent('plugins/page-builder::index', compact('page'));

        return view('plugins/page-builder::index', compact('page'));
    }

    /**
     * @param int $id
     * @param PageBuilderRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postSaveDesign($id, PageBuilderRequest $request, BaseHttpResponse $response)
    {
        $page = $this->pageRepository->findOrFail($id);

        $page->content = $request->input('content');

        $this->pageRepository->createOrUpdate($page);

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }
}
