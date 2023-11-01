<?php

declare(strict_types=1);

namespace Openai;

/**
 * @method static self tryFrom(mixed $model)
 * @property string $value
 */
enum Model: string
{
    /**
     * GPT-4 is a large multimodal model (accepting text inputs and emitting text outputs today, with image inputs
     * coming in the future) that can solve difficult problems with greater accuracy than any of our previous models,
     * thanks to its broader general knowledge and advanced reasoning capabilities. Like gpt-3.5-turbo, GPT-4 is
     * optimized for chat but works well for traditional completions tasks using the Chat completions API.
     */

    /**
     * More capable than any GPT-3.5 model, able to do more complex tasks, and optimized for chat. Will be updated with
     * our latest model iteration 2 weeks after it is released.
     */
    case GPT4 = 'gpt-4';

    /**
     * Same capabilities as the standard gpt-4 mode but with 4x the context length. Will be updated with our latest
     * model iteration.
     */
    case GPT4_32K = 'gpt-4-32k';

    /**
     * GPT-3.5 models can understand and generate natural language or code. Our most capable and cost effective model
     * in the GPT-3.5 family is gpt-3.5-turbo which has been optimized for chat using the Chat completions API but works
     * well for traditional completions tasks as well.
     */

    /**
     * Most capable GPT-3.5 model and optimized for chat at 1/10th the cost of text-davinci-003. Will be updated with
     * our latest model iteration 2 weeks after it is released.
     */
    case GPT3_5_TURBO = 'gpt-3.5-turbo';
    case GPT3_5_TURBO_0613 = 'gpt-3.5-turbo-0613';

    /**
     * Same capabilities as the standard gpt-3.5-turbo model but with 4 times the context.
     */
    case GPT3_5_TURBO_16K = 'gpt-3.5-turbo-16k';

    /**
     * Whisper is a general-purpose speech recognition model. It is trained on a large dataset of diverse audio and is
     * also a multi-task model that can perform multilingual speech recognition as well as speech translation and
     * language identification. The Whisper v2-large model is currently available through our API with the whisper-1
     * model name.
     */
    case WHISPER1 = 'whisper-1';

    /**
     * The Moderation models are designed to check whether content complies with OpenAI's usage policies. The models
     * provide classification capabilities that look for content in the following categories: hate, hate/threatening,
     * self-harm, sexual, sexual/minors, violence, and violence/graphic.
     */

    /**
     * Most capable moderation model. Accuracy will be slightly higher than the stable model.
     */
    case MODERATION_LATEST = 'text-moderation-latest';

    case TEXT_MODERATION_006 = 'text-moderation-006';

    /**
     * Almost as capable as the latest model, but slightly older.
     */
    case MODERATION_STABLE = 'text-moderation-stable';
}
