# 🌍 Countries & Capitals Quiz

Projeto desenvolvido como parte de estudos no **[Curso COMPLETO de Laravel 11 & 12 para Desenvolvimento Web Full Stack em PHP]** do professor João Ribeiro.  
O objetivo é praticar Laravel criando um jogo de perguntas e respostas sobre capitais do mundo.

---

## ⚙️ Como Funciona o Quiz

1. **Definir número de perguntas**  
   - Na tela inicial, o usuário digita quantas questões deseja responder.  
   - Ao clicar em **"Iniciar Questionário"**, o sistema gera aleatoriamente as perguntas a partir de um banco de dados de países e capitais.

2. **Responder as perguntas**  
   - Cada pergunta mostra um país e quatro opções de capitais.  
   - Apenas **uma alternativa** está correta.  
   - O usuário clica na resposta escolhida e automaticamente avança para a próxima questão.

3. **Controle de progresso**  
   - No topo da tela, é exibido o número da questão atual e o total de questões (ex.: *Pergunta: 3 / 5*).  
   - Também há a opção de **"Cancelar Jogo"** a qualquer momento, retornando ao início.

4. **Cálculo do resultado final**  
   - Ao final, o sistema exibe:
     - **Total de questões respondidas**
     - **Número de acertos**
     - **Número de erros**
     - **Percentual de aproveitamento**
   - Um botão **"Voltar ao Início"** permite reiniciar o jogo.


## 🚀 Recursos Utilizados

- **PHP** 8.2
- **Laravel** 12
- **Composer**
- **MySQL**
- **Node.js** + **NPM**
- **Bootstrap** para estilização
- **Vite** para build de assets

---

