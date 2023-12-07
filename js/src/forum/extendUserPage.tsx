import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import type Mithril from 'mithril';
import UserPage from 'flarum/forum/components/UserPage';
import LinkButton from 'flarum/common/components/LinkButton';
import ItemList from 'flarum/common/utils/ItemList';

export default function extendUserPage() {
  extend(UserPage.prototype, 'navItems', function (items: ItemList<Mithril.Children>) {
    if (app.session.user) {
      if (this.user?.id() === app.session.user.id() || app.session.user.id() == 1) {
        items.add(
          'signature',
          <LinkButton
            href={app.route('user.signature', { username: this.user?.username() })}
            icon="fas fa-signature"
            class="Button Button--link hasIcon"
          >
            {app.translator.trans('signature.forum.buttons.signature')}
          </LinkButton>,
          20
        );
      }
    }
  });
}
