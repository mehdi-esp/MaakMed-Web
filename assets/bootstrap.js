import { startStimulusApp } from '@symfony/stimulus-bridge';
import ScrollTo from '@stimulus-components/scroll-to'
import TextareaAutogrow from 'stimulus-textarea-autogrow'

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));
app.register('scroll-to', ScrollTo)
app.register('textarea-autogrow', TextareaAutogrow)
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
