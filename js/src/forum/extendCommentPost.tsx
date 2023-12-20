import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import CommentPost from 'flarum/forum/components/CommentPost';
import Signature from './components/Signature';

export default function extendCommentPost() {
  extend(CommentPost.prototype, 'content', function (content) {
    if (this.attrs.post.user?.()) {
      if (this.attrs.post.user().signature()) {
        const allowInlineEditing = app.forum.attribute('enableInlineEditing') || false;

        content.push(
          <div className="Post-signature">
            <Signature user={this.attrs.post.user()} readonly={!allowInlineEditing} />
          </div>
        );
      }
    }
  });
}
