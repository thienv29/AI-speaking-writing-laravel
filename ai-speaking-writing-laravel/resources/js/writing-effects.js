;(function (global) {
    const fallbackToneMap = {
        success: 880,
        failure: 330,
        button: 520,
    };

    const animationClassMap = {
        confetti: 'anim-confetti',
        shake: 'anim-shake',
        wobble: 'anim-shake',
        bounce: 'anim-bounce',
        sparkle: 'anim-sparkle',
        wave: 'anim-wave',
        pulse: 'anim-pulse',
        rainbow: 'anim-rainbow',
        tick: 'anim-pulse'
    };

    let audioContext = null;
    let audioUnlocked = false;
    const audioCache = {};

    function ensureAudioContext() {
        if (audioContext) return audioContext;
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return null;
        audioContext = new AudioCtx();
        if (audioContext.state === 'suspended') {
            audioContext.resume().catch(() => {});
        }
        return audioContext;
    }

    function unlockAudio() {
        if (audioUnlocked) {
            const ctx = ensureAudioContext();
            if (ctx && ctx.state === 'suspended') {
                ctx.resume().catch(() => {});
            }
            return;
        }

        const ctx = ensureAudioContext();
        if (ctx && ctx.state === 'suspended') {
            ctx.resume().then(() => {
                audioUnlocked = true;
            }).catch(() => {});
        } else if (ctx) {
            audioUnlocked = true;
        }

        try {
            const silentAudio = new Audio('data:audio/wav;base64,UklGRigAAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQAAAAA=');
            silentAudio.volume = 0.01;
            silentAudio.play().then(() => {
                audioUnlocked = true;
            }).catch(() => {});
        } catch (e) {}
    }

    function playSound(url, stage) {
        if (!url) {
            playFallbackTone(stage);
            return;
        }

        unlockAudio();

        try {
            let audio = audioCache[url];
            if (!audio) {
                audio = new Audio(url);
                audioCache[url] = audio;
                audio.addEventListener('error', () => playFallbackTone(stage));
                audio.load();
            }

            audio.currentTime = 0;
            const playPromise = audio.play();

            if (playPromise !== undefined) {
                playPromise.catch(() => playFallbackTone(stage));
            } else {
                playFallbackTone(stage);
            }
        } catch (error) {
            playFallbackTone(stage);
        }
    }

    function playFallbackTone(stage) {
        unlockAudio();

        let attempts = 0;
        const maxAttempts = 3;

        const tryPlayTone = () => {
            attempts++;
            const ctx = ensureAudioContext();
            if (!ctx) return;

            if (ctx.state === 'suspended') {
                ctx.resume().then(() => {
                    playTone(ctx, stage);
                }).catch(() => {
                    if (attempts < maxAttempts) {
                        setTimeout(tryPlayTone, 200);
                    }
                });
            } else {
                playTone(ctx, stage);
            }
        };

        tryPlayTone();
    }

    function playTone(ctx, stage) {
        const frequency = fallbackToneMap[stage] || fallbackToneMap.button;
        const oscillator = createOscillator(ctx, frequency);
        const gainNode = ctx.createGain();
        gainNode.gain.setValueAtTime(0.15, ctx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);

        oscillator.connect(gainNode);
        gainNode.connect(ctx.destination);
        oscillator.start();
        oscillator.stop(ctx.currentTime + 0.25);
    }

    function createOscillator(ctx, frequency) {
        const oscillator = ctx.createOscillator();
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(frequency, ctx.currentTime);
        return oscillator;
    }

    function getStageEffect(effectData, stage) {
        if (effectData && effectData[stage]) {
            return effectData[stage];
        }
        return null;
    }

    function applyEffect(stageEffect, stage) {
        if (!stageEffect) {
            stageEffect = {};
        }

        const effectTarget = document.querySelector('.question-panel');
        if (!effectTarget) return;

        const animationClasses = Object.values(animationClassMap);
        animationClasses.forEach(cls => effectTarget.classList.remove(cls));

        const animationKey = stageEffect.animation || (stage === 'success' ? 'confetti' : 'shake');
        const className = animationClassMap[animationKey];
        if (className) {
            effectTarget.classList.add(className);
        }

        playStageSound(stageEffect, stage);
    }

    function playStageSound(stageEffect, stage) {
        if (stageEffect && stageEffect.sound) {
            playSound(stageEffect.sound, stage);
        } else {
            playFallbackTone(stage);
        }
    }

    global.WritingEffects = {
        playSound,
        playFallbackTone,
        applyEffect,
        getStageEffect,
        unlockAudio,
    };
})(window);

