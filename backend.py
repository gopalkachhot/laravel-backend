#!/usr/bin/env python
import sys

single_card_fracs = {0: (17156428, 25992, 6308180, 23490600),
                     1: (14719370, 25992, 8745238, 23490600),
                     2: (13171718, 25992, 10292890, 23490600),
                     3: (12141064, 25992, 11323544, 23490600),
                     4: (11480656, 25992, 11983952, 23490600),
                     5: (11066588, 25992, 12398020, 23490600),
                     6: (10809138, 25992, 12655470, 23490600),
                     7: (10645478, 25992, 12819130, 23490600),
                     8: (10534476, 25992, 12930132, 23490600),
                     9: (10451724, 25992, 13012884, 23490600),
                     10: (10383320, 25992, 13081288, 23490600),
                     11: (10154288, 25992, 13310320, 23490600),
                     12: (9805704, 25992, 13658904, 23490600)}


def split_cards(cards):
    a_cards = []
    b_cards = []
    for i, x in enumerate(cards):
        if i & 1:
            b_cards.append(x)
        else:
            a_cards.append(x)

    return a_cards, b_cards

def generate_52_cards():
    values = ['a', 'k', 'q', 'j', 't'] + [str(x) for x in range(9, 1, -1)]
    suits = ('c', 'd', 'h', 's')

    cards = []
    for value in values:
        for suit in suits:
            cards.append(value + suit)
    return cards

def gen_card_range(i, cards, a_cards, idx):
    if len(a_cards) >= i:
        return [a_cards[i - 1]], False

    return list(set(range(idx, 52)) - set(cards)), True

def precalculate_cost():
    cost = {}
    for c1 in range(52):
        for c2 in range(52):
            for c3 in range(52):
                if c1 > c2 or c2 > c3:
                    cards = sorted([c1, c2, c3])
                    cost[(c1, c2, c3)] = cost[tuple(cards)]
                else:
                    x1 = c1 / 4
                    x2 = c2 / 4
                    x3 = c3 / 4

                    if x1 == x2 and x2 == x3:
                        cost[(c1, c2, c3)] = (0, x1)
                        continue
                    suited = (c2 - c1) % 4 == 0 and (c3 - c2) % 4 == 0
                    sequence = x2 == x1 + 1 and x3 == x2 + 1 or \
                               x1 == 0 and x2 == 11 and x3 == 12
                    pair = False
                    pair_val = None
                    if x1 == x2:
                        pair = True
                        pair_val = x1, x3
                    elif x2 == x3:
                        pair = True
                        pair_val = x2, x1

                    if suited and sequence:
                        # hardcode for making Ac2c3c higher than anything
                        if x1 == 0 and x2 == 11 and x3 == 12:
                            cost[(c1, c2, c3)] = (1, (0, 0, 0))
                        else:
                            cost[(c1, c2, c3)] = (1, (x1, x2, x3))
                        continue

                    if sequence:
                        if x1 == 0 and x2 == 11 and x3 == 12:
                            cost[(c1, c2, c3)] = (2, (0, 0, 0))
                        else:
                            cost[(c1, c2, c3)] = (2, (x1, x2, x3))
                        continue

                    if suited:
                        cost[(c1, c2, c3)] = (3, (x1, x2, x3))
                        continue

                    if pair:
                        cost[(c1, c2, c3)] = (4, pair_val)
                        continue
                    cost[(c1, c2, c3)] = (5, (x1, x2, x3))
    return cost

def calculate_probability(cards):
    all_cards = generate_52_cards()
    cost = precalculate_cost()
    card_to_int = {x: i for i, x in enumerate(all_cards)}
    int_to_card = {i: x for i, x in enumerate(all_cards)}

    cards = [card_to_int[x] for x in cards]

    if len(cards) == 1:
        x = cards[0] / 4
        #return single_card[x]
        return single_card_fracs[x]

    a_cards, b_cards = split_cards(cards)
    a1 = a_cards[0]

    a2_range, a2_flag = gen_card_range(2, cards, a_cards, 0)

    #total = 0.0
    total = 0
    win, draw, lose = 0, 0, 0
    for a2 in a2_range:
        a2_idx = a2 + 1 if a2_flag else 0
        a3_range, a3_flag = gen_card_range(3, cards + [a2], a_cards, a2_idx)
        for a3 in a3_range:
            b1_range, b1_flag = gen_card_range(1, cards + [a2, a3], b_cards, 0)
            cost1 = cost[(a1, a2, a3)]
            for b1 in b1_range:
                b1_idx = b1 + 1 if b1_flag else 0
                b2_range, b2_flag = gen_card_range(2, cards + [a2, a3, b1],
                                          b_cards, b1_idx)
                for b2 in b2_range:
                    b2_idx = b2 + 1 if b2_flag else 0
                    b3_range, b3_flag = gen_card_range(3, cards + [a2, a3, b1, b2],
                                              b_cards, b2_idx)
                    for b3 in b3_range:
                        #if len(set((a1, a2, a3, b1, b2, b3))) != 6:
                        #    print("Issue with set length")
                        cost2 = cost[(b1, b2, b3)]
                        total += 1
                        if cost1 < cost2:
                            win += 1
                        elif cost1 > cost2:
                            lose += 1
                        else:
                            draw += 1

    #print("total: {0}".format(total))
    #return win / total * 100, draw / total * 100, lose / total * 100
    return win, draw, lose, total

def gcd(a, b):
    if a == 0:
        return b
    return gcd(b % a, a)

def calculate_odds(num, denom):
    x = num
    y = denom - num

    g = gcd(x, y)

    x /= g
    y /= g
    return x, y

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print('Input cards not specified. Please use syntax '
              'python backend.py "Ah Ks Qd"')
        exit(1)
    cards_str = sys.argv[1]
    tokens = cards_str.split(" ")
    cards = [x.lower() for x in tokens if x]

    """
    ans = {}
    for x in range(13):
        c = x * 4
        win, draw, lose, total = calculate_probability([c])
        ans[x] = win, draw, lose, total

    print(ans)
    import pdb; pdb.set_trace()
    """
    #import pdb; pdb.set_trace()
    #print(cards)
    win, draw, lose, total = calculate_probability(cards)

    """
    w1 = 100.0 * win / total
    d1 = 100.0 * draw / total
    l1 = 100.0 * lose / total
    print("win: {0:.2f}% draw: {1:.2f}% lose: {2:.2f}%".format(w1, d1, l1))
    """

    #print(win, draw, lose, total)
    """
    win = 10
    draw = 5
    lose = 85
    total = 100
    """
    for name, x in zip(('PLAYER A', 'DRAW', 'PLAYER B'), (win, draw, lose)):
        num, denom = calculate_odds(x, total)
        if num == 0:
            print("{0}: -".format(name))
        else:
            frac_odds = (.0 + denom) / num
            print("{0}: {1:.2f}".format(name, frac_odds))
    #print(win, draw, lose, total)
    #print("win: {0:.2f}% draw: {1:.2f}% lose: {2:.2f}%".format(win, draw, lose))
