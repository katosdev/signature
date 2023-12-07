import app from 'flarum/forum/app';
import extendUserPage from './extendUserPage';
import extendCommentPost from './extendCommentPost';

export { default as extend } from './extend';

app.initializers.add('katosdev-signature', () => {
  extendUserPage();
  extendCommentPost();
});
