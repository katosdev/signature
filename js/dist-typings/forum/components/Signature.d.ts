import Component, { ComponentAttrs } from 'flarum/common/Component';
import User from 'flarum/common/models/User';
import SignatureState from '../states/SignatureState';
import type Mithril from 'mithril';
interface SignatureAttrs extends ComponentAttrs {
    user: User;
    readonly?: boolean;
    state?: SignatureState;
}
export default class Signature extends Component<SignatureAttrs> {
    signatureState: SignatureState;
    user: User;
    loading: boolean;
    oninit(vnode: Mithril.Vnode<SignatureAttrs, this>): void;
    view(): JSX.Element;
    renderEditor(): JSX.Element | undefined;
    onEditorSubmit(): void;
    renderSignature(): JSX.Element;
    edit(): void;
    save(): void;
}
export {};
