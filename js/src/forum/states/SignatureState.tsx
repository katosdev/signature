import Stream from 'flarum/common/utils/Stream';

class SignatureState {
  // The content of the signature
  content: Stream<string>;

  // Indicates whether the signature is being edited
  editing: boolean;

  constructor() {
    this.content = Stream('');
    this.editing = false;
  }

  // Sets the content of the signature
  setContent(content: Stream<string>) {
    this.content = content;
  }

  // Toggles the editing state
  toggleEditing() {
    this.editing = !this.editing;
  }
}

export default SignatureState;
