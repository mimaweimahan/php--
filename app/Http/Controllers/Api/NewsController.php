<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\News;
use App\NewsCategory;

class NewsController extends Controller
{

    public function get(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error('参数错误');
        }
        $news = News::find($id);
        return $this->success($news);
    }

    //帮助中心,新闻分类
    public function getCategory()
    {
        $results = NewsCategory::where('is_show', 1)->orderBy('sorts')->get(['id', 'name'])->toArray();
        return $this->success($results);
    }

    //推荐新闻
    public function recommend()
    {
        $results = News::where('recommend', 1)->orderBy('id', 'desc')->get(['id', 'title', 'c_id'])->toArray();
        return $this->success($results);
    }

    //首页banner
    public function getBanner(Request $request){
        $lang = $request->get('lang');
        $results = News::where('c_id',5)->where('lang',$lang)->orderBy('id', 'desc')->get(['id','thumbnail'])->toArray();
        return $this->success($results);
    }


    public function getTextsList(Request $request){
        $type = $request->get('type');
        $lang = $request->get('lang');
        $id = "";
        if($type=='rule'){
            if($lang == "en"){
                $id = 93;
            }else{
                $id = 76;
            }
        }elseif($type == 'bz_rule'){
            if($lang == "en"){
                $id = 150;
            }else{
                $id = 149;
            }
        }elseif($type== "about"){
            if($lang == "en"){
                $id = 137;
            }else{
                $id = 89;
            }
        }


//        $id = $request->get('id', 0);
        if (empty($id)) {
            return $this->error('参数错误');
        }
        $news = News::find($id);
        return $this->success($news);
    }

    public function getGG(Request $request){

        $lang = $request->get('lang');
        $results = News::where('c_id',4)->where('lang',$lang)->orderBy('id', 'desc')->get(['id','title'])->toArray();
        return $this->success($results);
    }

    // 获取分类下的文章
    public function getArticle(Request $request)
    {
        $limit = $request->get('limit', 15);
        $page = $request->get('page', 1);
        $category_id = $request->get('c_id');
        $lang = $request->get('lang', '') ?: session()->get('lang');
        $lang == '' && $lang = 'zh';
        if (empty($category_id)) {
            $article = News::where('lang', $lang)
                ->orderBy('sorts', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($limit);
        } else {
            $article = News::where('lang', $lang)
                ->where('c_id', $category_id)
                ->orderBy('sorts', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);
        }
        //dd($article);
        foreach ($article->items() as &$value) {
            unset($value->content);
            unset($value->recommend);
            unset($value->display);
            unset($value->discuss);
            unset($value->author);
            unset($value->audit);
            unset($value->browse_grant);
            unset($value->keyword);
            unset($value->abstract);
            unset($value->views);
//            unset($value->create_time);
            unset($value->update_time);
        }
        return $this->success(array(
            "list" => $article->items(), 'count' => $article->total(),
            "page" => $page, "limit" => $limit
        ));
    }

    //获取返佣规则新闻
    public function getInviteReturn()
    {

        $c_id = 23;//返佣类型
        $news = News::where('c_id', $c_id)->orderBy('id', 'desc')->first();
        if (empty($news)) {
            return $this->error('新闻不存在');
        }
        $data['news'] = $news;
        //相关新闻
        $article = News::where('c_id', $c_id)->where('id', '<>', $news->id)->orderBy('id', 'desc')->get(['id', 'c_id', 'title'])->toArray();

        $data['relation_news'] = $article;
        return $this->success($data);
    }
}
