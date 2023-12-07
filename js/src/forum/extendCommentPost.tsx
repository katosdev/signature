import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import type Mithril from 'mithril';
import CommentPost from 'flarum/forum/components/CommentPost';
import Signature from './components/Signature';

export default function extendCommentPost() {
  extend(CommentPost.prototype, 'view', function (vnode: Mithril.Vnode) {
    if (this.attrs.post.user()) {
      if (this.attrs.post.user().signature()) {
        vnode.children.push(<Signature user={this.attrs.post.user()} readonly={true} />);
      }
    }
  });
}
