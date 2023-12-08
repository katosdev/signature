import UserPage from 'flarum/forum/components/UserPage';
import type Mithril from 'mithril';
import ItemList from 'flarum/common/utils/ItemList';
import SignatureState from '../states/SignatureState';
export default class SignaturePage extends UserPage {
    signatureState: SignatureState;
    oninit(vnode: Mithril.Vnode): void;
    content(): JSX.Element;
    controlItems(): ItemList<Mithril.Children>;
    onEdit(): void;
}
