import Stream from 'flarum/common/utils/Stream';
declare class SignatureState {
    content: Stream<string>;
    editing: boolean;
    constructor();
    setContent(content: Stream<string>): void;
    toggleEditing(): void;
}
export default SignatureState;
